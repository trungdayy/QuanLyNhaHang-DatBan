<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class NVDatBanController extends Controller
{
    /**
     * Hiển thị danh sách đặt bàn.
     */
    public function index(Request $r)
    {
        $query = DatBan::with(['banAn', 'nhanVien', 'combos', 'orderMon']);

        if ($r->trang_thai) {
            $query->where('trang_thai', $r->trang_thai);
        }

        if ($r->ban) {
            $query->whereHas('banAn', function ($q) use ($r) {
                $q->where('so_ban', 'like', "%{$r->ban}%");
            });
        }

        if ($r->khach) {
            $query->where(function ($q) use ($r) {
                $q->where('ten_khach', 'like', "%{$r->khach}%")
                    ->orWhere('sdt_khach', 'like', "%{$r->khach}%");
            });
        }

        if ($r->ma) {
            $query->where('ma_dat_ban', 'like', '%' . $r->ma . '%');
        }
        if ($r->la_dat_online !== null && $r->la_dat_online !== '') {
            $query->where('la_dat_online', $r->la_dat_online);
        }

        $ds = $query->orderByDesc('id')->get();
        if ($r->ajax()) {
            return view('shop.nhanvien.datban.tbody', compact('ds'));
        }
        return view('shop.nhanvien.datban.index', compact('ds'));
    }

    /**
     * Hiển thị form tạo mới đặt bàn.
     */
    public function create()
    {
        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        // Lấy danh sách nhân viên để chọn người phụ trách
        $nhanViens = NhanVien::where('trang_thai', 1)
            ->whereIn('vai_tro', ['le_tan', 'phuc_vu', 'quan_ly'])
            ->get();
        return view('shop.nhanvien.datban.create', compact('combos', 'nhanViens'));
    }

    /**
     * AJAX: Kiểm tra các bàn còn trống.
     * Đã FIX lỗi không hiện bàn do đơn online chưa xếp bàn (ban_id = null)
     */
    public function ajaxCheckBanTrong(Request $request)
    {
        $selectedTime = $request->input('time') ? Carbon::parse($request->input('time')) : Carbon::now();
        $soKhach = $request->input('so_khach', 1);

        // Mặc định kiểm tra khung giờ 120 phút
        $defaultDuration = 120;
        $newStart = $selectedTime->copy();
        $newEnd = $newStart->copy()->addMinutes($defaultDuration);

        // 1. Tìm các bàn đang bị kẹt lịch
        $conflictingIds = DatBan::whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->whereNotNull('ban_id') // <--- FIX QUAN TRỌNG: Chỉ xét các đơn đã được xếp bàn
            ->where(function ($query) use ($newStart, $newEnd, $defaultDuration) {
                // Logic trùng lịch: (StartA < EndB) && (EndA > StartB)
                $query->where('gio_den', '<', $newEnd)
                    ->whereRaw(
                        "DATE_ADD(gio_den, INTERVAL IFNULL(thoi_luong_phut, ?) MINUTE) > ?",
                        [$defaultDuration, $newStart]
                    );
            })
            ->pluck('ban_id')
            ->toArray();

        // 2. Lấy danh sách bàn trống
        $availableTables = BanAn::where('trang_thai', 'trong') 
            ->where('trang_thai', '!=', 'khong_su_dung')
            ->whereNotIn('id', $conflictingIds)
            ->where('so_ghe', '>=', $soKhach)
            ->orderBy('so_ghe', 'asc') // Ưu tiên bàn nhỏ vừa đủ trước
            ->orderBy('so_ban', 'asc')
            ->get(['id', 'so_ban', 'so_ghe', 'trang_thai']);

        return response()->json($availableTables);
    }

    /**
     * Xử lý lưu đơn đặt bàn mới.
     */
    public function store(Request $request)
    {
        $now = Carbon::now();

        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'email_khach' => 'nullable|email|max:255',
            'nguoi_lon' => 'required|integer|min:0',
            'tre_em' => 'required|integer|min:0',
            'tong_khach' => 'required|integer|min:1',
            'ban_id' => [
                'required',
                'exists:ban_an,id',
                Rule::exists('ban_an', 'id')->where(fn($query) => $query->where('trang_thai', '!=', 'khong_su_dung')),
            ],
            'combos' => 'nullable|array',
            'combos.*.id' => 'required|exists:combo_buffet,id',
            'combos.*.so_luong' => 'required|integer|min:1',
            'gio_den' => 'required|date',
            'ghi_chu' => 'nullable|string',
            'nhan_vien_id' => 'nullable|exists:nhan_vien,id',
        ], [
            'tong_khach.min' => 'Tổng số khách phải ít nhất là 1.',
            'ban_id.exists' => 'Bàn được chọn không hợp lệ hoặc đang bảo trì.',
        ]);

        $tongKhach = $request->nguoi_lon + $request->tre_em;
        
        // Tính tổng số lượng combo khách chọn
        $tongCombo = collect($request->input('combos', []))->sum('so_luong');

        // [LOGIC MỚI] 
        // 1. Nếu khách có chọn combo ($tongCombo > 0) => Bắt buộc số combo >= số người
        // 2. Nếu khách KHÔNG chọn combo ($tongCombo == 0) => Cho qua (Gọi món lẻ)
        if ($tongCombo > 0 && $tongCombo < $tongKhach) {
            return back()->withInput()->with(
                'error',
                "Bạn có {$tongKhach} khách nhưng chỉ chọn {$tongCombo} suất Combo. Quy định: Nếu dùng Combo, số suất phải lớn hơn hoặc bằng số khách."
            );
        }

        $banAn = BanAn::find($request->ban_id);

        if ($banAn->so_ghe < $tongKhach) {
            return back()->withInput()->with('error', "Bàn này chỉ có {$banAn->so_ghe} ghế, không đủ cho {$tongKhach} khách.");
        }

        // Tính thời lượng
        $thoiLuongPhut = 120; // Mặc định
        if (!empty($request->combos)) {
             $ids = collect($request->combos)->pluck('id');
             $maxTime = ComboBuffet::whereIn('id', $ids)->max('thoi_luong_phut');
             if ($maxTime) $thoiLuongPhut = $maxTime;
        }

        // Kiểm tra trùng lịch lần cuối (Double check)
        $conflict = DatBan::where('ban_id', $request->ban_id)
            ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->where(function ($query) use ($request, $thoiLuongPhut) {
                $newStart = Carbon::parse($request->gio_den);
                $newEnd = $newStart->copy()->addMinutes($thoiLuongPhut);

                $query->where('gio_den', '<', $newEnd)
                    ->whereRaw(
                        "DATE_ADD(gio_den, INTERVAL IFNULL(thoi_luong_phut, ?) MINUTE) > ?",
                        [$thoiLuongPhut, $newStart]
                    );
            })
            ->first();

        if ($conflict) {
            $gio = Carbon::parse($conflict->gio_den)->format('H:i');
            return back()->withInput()->with('error', "Bàn {$banAn->so_ban} vừa bị đặt lúc {$gio} bởi đơn #{$conflict->ma_dat_ban}. Vui lòng chọn bàn khác.");
        }

        DB::beginTransaction();
        try {
            $maDatBan = 'DB-' . $now->format('YmdHis') . '-' . strtoupper(Str::random(4));
            
            $datBan = DatBan::create([
                'ma_dat_ban' => $maDatBan,
                'ten_khach' => $request->ten_khach,
                'email_khach' => $request->email_khach,
                'sdt_khach' => $request->sdt_khach,
                'nguoi_lon' => $request->nguoi_lon,
                'tre_em' => $request->tre_em,
                'ban_id' => $request->ban_id,
                'gio_den' => Carbon::parse($request->gio_den),
                'thoi_luong_phut' => $thoiLuongPhut,
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => 'khach_da_den', // Nhân viên tạo -> Khách đến ngay
                'nhan_vien_id' => $request->nhan_vien_id ?? Auth::id(),
                'tien_coc' => 0,
                'la_dat_online' => 0,
            ]);

            // Lưu Combo (chỉ lưu nếu có chọn combo)
            if (!empty($request->combos)) {
                $combosToAttach = [];
                foreach ($request->combos as $combo) {
                    if (isset($combo['id']) && isset($combo['so_luong']) && $combo['so_luong'] > 0) {
                        $combosToAttach[$combo['id']] = ['so_luong' => $combo['so_luong']];
                    }
                }
                if (!empty($combosToAttach)) {
                    $datBan->combos()->attach($combosToAttach);
                }
            }

            // Cập nhật trạng thái bàn thành "đang phục vụ"
            if ($banAn->trang_thai === 'trong') {
                $banAn->update(['trang_thai' => 'dang_phuc_vu']);
            }

            DB::commit();

            return redirect()->route('nhanVien.datban.index')
                ->with('success', 'Tạo đặt bàn thành công! Mã: ' . $maDatBan);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi lưu Đặt bàn NV: " . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Thay đổi trạng thái đặt bàn.
     */
    public function thayDoiTrangThai(Request $request, DatBan $datBan)
    {
        $trangThaiMoi = $request->input('trang_thai');
        $message = '';

        $trangThaiHopLe = ['da_xac_nhan', 'huy', 'khach_da_den', 'hoan_tat'];
        if (!in_array($trangThaiMoi, $trangThaiHopLe)) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
        }

        $banAn = $datBan->banAn;

        DB::beginTransaction();
        try {
            switch ($trangThaiMoi) {
                case 'da_xac_nhan':
                    if ($datBan->trang_thai !== 'cho_xac_nhan') {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Chỉ có thể xác nhận đơn đặt bàn ở trạng thái Chờ xác nhận.');
                    }
                    
                    if ($banAn) {
                        $banAn->trang_thai = 'da_dat';
                        $banAn->save();
                    }

                    if (empty($datBan->nhan_vien_id)) {
                        $datBan->nhan_vien_id = Auth::id();
                    }
                    $message = 'Đã xác nhận đặt bàn thành công.';
                    break;

                case 'khach_da_den':
                    // Chấp nhận chuyển từ "Đã xác nhận" hoặc "Chờ xác nhận"
                    if (!in_array($datBan->trang_thai, ['da_xac_nhan', 'cho_xac_nhan'])) {
                        // Tùy logic quán, nhưng thường là vậy
                    }

                    if ($banAn) {
                        $banAn->trang_thai = 'dang_phuc_vu';
                        $banAn->save();
                    }

                    $message = 'Khách đã đến, bắt đầu phục vụ.';
                    break;

                case 'huy':
                    if ($banAn) {
                        // Kiểm tra xem bàn này có đơn nào khác đang dùng không (tránh trường hợp hủy đơn cũ làm trống bàn đang có khách khác ngồi)
                        $isTableStillInUse = DatBan::where('ban_id', $datBan->ban_id)
                            ->where('id', '!=', $datBan->id)
                            ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
                            ->exists();
                        
                        if (!$isTableStillInUse && $banAn->trang_thai !== 'khong_su_dung') {
                            $banAn->trang_thai = 'trong';
                            $banAn->save();
                        }
                    }
                    $message = 'Đã hủy đơn đặt bàn.';
                    break;

                case 'hoan_tat':
                    if ($datBan->trang_thai !== 'khach_da_den') {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Chỉ có thể Hoàn tất khi khách đang được phục vụ.');
                    }

                    if ($banAn) {
                        $banAn->trang_thai = 'trong';
                        $banAn->save();
                    }

                    $datBan->trang_thai = $trangThaiMoi;
                    $datBan->save();

                    DB::commit();
                    
                    return redirect()->route('nhanVien.hoadon.create', ['dat_ban_id' => $datBan->id])
                        ->with('success', 'Kết thúc phục vụ. Chuyển sang thanh toán.');
            }

            if ($trangThaiMoi !== 'hoan_tat') {
                $datBan->trang_thai = $trangThaiMoi;
                $datBan->save();
                DB::commit();
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi thay đổi trạng thái Đặt bàn: ID={$datBan->id}, Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}