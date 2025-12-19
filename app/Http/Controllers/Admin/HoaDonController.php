<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HoaDon;
use App\Models\DatBan;
use App\Models\Voucher;

class HoaDonController extends Controller
{
    public function index(Request $request)
    {
        $query = HoaDon::with([
            'datBan.banAn',
            'datBan.chiTietDatBan.combo',
            'datBan.orderMon.chiTietOrders.monAn',
            'voucher',
            'chiTietHoaDon'
        ])->latest();

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            
            $query->where(function ($q) use ($searchTerm) {
                $q->where('ma_hoa_don', 'LIKE', $searchTerm)
                  ->orWhereHas('datBan', function ($datBanQuery) use ($searchTerm) {
                      $datBanQuery->where('ten_khach', 'LIKE', $searchTerm);
                  })
                  ->orWhereHas('datBan.banAn', function ($banAnQuery) use ($searchTerm) {
                      $banAnQuery->where('so_ban', 'LIKE', $searchTerm);
                  });
            });
        }

        if ($request->filled('phuong_thuc_tt')) {
            $query->where('phuong_thuc_tt', $request->phuong_thuc_tt);
        }

        $hoadons = $query->paginate(10);
        
        $hoadons->appends($request->query());

        return view('admins.hoa-don.index', compact('hoadons'));
    }

    public function create()
    {
        // Đã sửa: 'comboBuffet' -> 'combos'
        $datBans = DatBan::with('banAn', 'combos', 'orderMon')
            ->where('trang_thai', 'hoan_tat')
            ->whereDoesntHave('hoaDon') // Sửa ở đây
            ->get();
            
        $vouchers = Voucher::where('trang_thai', 'dang_ap_dung')
            ->where('ngay_ket_thuc', '>=', now())
            ->whereRaw('so_luong > so_luong_da_dung')
            ->get();

        return view('admins.hoa-don.create', compact('datBans', 'vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dat_ban_id' => 'required|exists:dat_ban,id|unique:hoa_don,dat_ban_id',
            'phuong_thuc_tt' => 'required|string',
            'phu_thu' => 'nullable|numeric|min:0',
            'voucher_id' => 'nullable|exists:vouchers,id'
        ]);

        $datBan = DatBan::with('orderMon', 'banAn')->findOrFail($request->dat_ban_id);
        $voucher = $request->voucher_id ? Voucher::find($request->voucher_id) : null;

        $tongTienOrder = $datBan->orderMon->sum('tong_tien');
        $tienCoc = (float) ($datBan->tien_coc ?? 0);
        $phuThu = (float) ($request->phu_thu ?? 0);

        $tienGiam = 0;
        if ($voucher) {
            if ($voucher->loai_giam == 'phan_tram') {
                $tienGiam = $tongTienOrder * ($voucher->gia_tri / 100);
                if ($voucher->gia_tri_toi_da && $tienGiam > $voucher->gia_tri_toi_da) {
                    $tienGiam = $voucher->gia_tri_toi_da;
                }
            } else {
                $tienGiam = $voucher->gia_tri;
            }
            if ($tienGiam > $tongTienOrder) {
                $tienGiam = $tongTienOrder;
            }
        }

        $daThanhToan = $tongTienOrder - $tienGiam + $phuThu - $tienCoc;
        if ($daThanhToan < 0) $daThanhToan = 0;

        $hoaDon = HoaDon::create([
            'dat_ban_id' => $datBan->id,
            'voucher_id' => $voucher ? $voucher->id : null,
            'ma_hoa_don' => 'HD' . date('Ymd-') . $datBan->id,
            'tong_tien' => $tongTienOrder,
            'tien_giam' => $tienGiam,
            'phu_thu' => $phuThu,
            'da_thanh_toan' => $daThanhToan,
            'phuong_thuc_tt' => $request->phuong_thuc_tt,
        ]);
        
        if ($voucher) {
            $voucher->increment('so_luong_da_dung');
        }

        if ($datBan->banAn) {
            $datBan->banAn->update(['trang_thai' => 'trong']);
        }

        return redirect()->route('admin.hoa-don.index')
            ->with('success', 'Tạo hóa đơn thành công!');
    }

    public function show($id)
    {
        // Đã sửa: 'datBan.comboBuffet' -> 'datBan.combos'
        $hoaDon = HoaDon::with([
            'datBan.banAn.khuVuc',
            'datBan.comboBuffet',
            'datBan.chiTietDatBan.combo',
            'datBan.orderMon.chiTietOrders.monAn',
            'voucher',
            'chiTietHoaDon'
        ])->findOrFail($id);

        return view('admins.hoa-don.show', compact('hoaDon'));
    }

    public function edit($id)
    {
        $hoaDon = HoaDon::with([
            'datBan.banAn.khuVuc',
            'datBan.chiTietDatBan.combo',
            'datBan.orderMon.chiTietOrders.monAn',
            'voucher',
            'chiTietHoaDon'
        ])->findOrFail($id);
        
        return view('admins.hoa-don.edit', compact('hoaDon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'phuong_thuc_tt' => 'required|string',
            'trang_thai' => 'required|in:da_thanh_toan,chua_thanh_toan',
            'tien_khach_dua' => 'nullable|numeric|min:0'
        ]);

        $hoaDon = HoaDon::with(['chiTietHoaDon', 'datBan'])->findOrFail($id);
        
        // Lấy thông tin từ chi_tiet_hoa_don nếu có
        $chiTiet = $hoaDon->chiTietHoaDon;
        $tongTienComboMon = $chiTiet->tong_tien_combo_mon ?? $hoaDon->tong_tien ?? 0;
        $tienGiamVoucher = $chiTiet->tien_giam_voucher ?? $hoaDon->tien_giam ?? 0;
        $tienCoc = $chiTiet->tien_coc ?? $hoaDon->datBan->tien_coc ?? 0;
        $tongPhuThu = $chiTiet->tong_phu_thu ?? $hoaDon->phu_thu ?? 0;
        
        $phaiThanhToan = $tongTienComboMon - $tienGiamVoucher - $tienCoc + $tongPhuThu;
        if ($phaiThanhToan < 0) $phaiThanhToan = 0;
        
        // Xử lý tiền khách đưa và tiền trả lại
        $tienKhachDua = 0;
        $tienTraLai = 0;
        
        if ($request->phuong_thuc_tt == 'tien_mat' && $request->filled('tien_khach_dua')) {
            $tienKhachDua = (float) $request->tien_khach_dua;
            $tienTraLai = max(0, $tienKhachDua - $phaiThanhToan);
        }
        
        // Cập nhật hóa đơn
        $hoaDon->update([
            'phuong_thuc_tt' => $request->phuong_thuc_tt,
            'trang_thai' => $request->trang_thai,
            'da_thanh_toan' => $phaiThanhToan,
        ]);
        
        // Cập nhật chi tiết hóa đơn nếu có
        if ($chiTiet) {
            $chiTiet->update([
                'phuong_thuc_tt' => $request->phuong_thuc_tt,
                'tien_khach_dua' => $tienKhachDua,
                'tien_tra_lai' => $tienTraLai,
            ]);
        }

        return redirect()->route('admin.hoa-don.index')
            ->with('success', 'Cập nhật hóa đơn thành công!');
    }

    public function destroy($id)
    {
        $hoaDon = HoaDon::with('voucher')->findOrFail($id);
        
        if ($hoaDon->voucher) {
            $hoaDon->voucher->decrement('so_luong_da_dung');
        }
        
        $hoaDon->delete();

        return redirect()->route('admin.hoa-don.index')
            ->with('success', 'Xóa hóa đơn thành công!');
    }
}