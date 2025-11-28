<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\NhanVien;
use App\Models\OrderMon;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class NVDatBanController extends Controller
{
public function index(Request $r)
{
    // Bắt đầu query builder, chưa gọi get()
    $query = DatBan::with(['banAn', 'nhanVien', 'comboBuffet']);

    // Lọc trạng thái
    if ($r->trang_thai) {
        $query->where('trang_thai', $r->trang_thai);
    }

    // Lọc theo bàn
    if ($r->ban) {
        $query->whereHas('banAn', function ($q) use ($r) {
            $q->where('so_ban', 'like', "%{$r->ban}%");
        });
    }

    // Lọc theo khách
    if ($r->khach) {
        $query->where(function($q) use ($r) {
            $q->where('ten_khach', 'like', "%{$r->khach}%")
              ->orWhere('sdt_khach', 'like', "%{$r->khach}%");
        });
    }

    // Lọc theo mã đặt bàn (thay ngày)
    if ($r->ma) {
        $query->where('ma_dat_ban', 'like', '%' . $r->ma . '%');
    }

    // Chỉ gọi orderByDesc trước khi get
    $ds = $query->orderByDesc('id')->get();

    return view('shop.nhanvien.datban.index', compact('ds'));
}

    public function create()
    {
        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        $nhanViens = NhanVien::where('trang_thai', 1)
            ->whereIn('vai_tro', ['le_tan', 'phuc_vu'])
            ->get();
        return view('shop.nhanvien.datban.create', compact('combos', 'nhanViens'));
    }

    public function ajaxCheckBanTrong(Request $request)
    {
        $selectedTime = $request->input('time');
        $soKhach = $request->input('so_khach', 1);

        if (!$selectedTime) {
            return response()->json(['error' => 'Vui lòng chọn giờ.'], 400);
        }

        $duration = 120;
        $newStart = Carbon::parse($selectedTime);
        $newEnd = $newStart->copy()->addMinutes($duration);

        $conflictingIds = DatBan::whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->where('gio_den', '<', $newEnd)
                      ->whereRaw("DATE_ADD(gio_den, INTERVAL thoi_luong_phut MINUTE) > ?", [$newStart]);
            })
            ->pluck('ban_id')
            ->toArray();

        $availableTables = BanAn::where('trang_thai', 'trong')
            ->whereNotIn('id', $conflictingIds)
            ->where('so_ghe', '>=', $soKhach)
            ->orderBy('so_ban')
            ->get(['id', 'so_ban', 'so_ghe']);

        return response()->json($availableTables);
    }

public function store(Request $request)
{
    $now = Carbon::now();

    $request->validate([
        'ten_khach' => 'required|string|max:255',
        'sdt_khach' => 'nullable|string|max:20',
        'email_khach' => 'nullable|email|max:255',
        'so_khach' => 'required|integer|min:1',
        'ban_id' => [
            'required',
            'exists:ban_an,id',
            Rule::exists('ban_an', 'id')->where(fn($query) => $query->where('trang_thai', '!=', 'khong_su_dung')),
        ],
        'combo_id' => 'nullable|exists:combo_buffet,id',
        'gio_den' => 'required|date',
        'ghi_chu' => 'nullable|string',
        'nhan_vien_id' => 'nullable|exists:nhan_vien,id',
    ], [
        'ban_id.exists' => 'Bàn được chọn không hợp lệ hoặc đang bảo trì.',
    ]);

    $banAn = BanAn::find($request->ban_id);

    if ($banAn->so_ghe < $request->so_khach) {
        return back()->with('error', "Bàn này chỉ có {$banAn->so_ghe} ghế, không đủ cho {$request->so_khach} khách.");
    }

    // Kiểm tra xung đột giờ đặt bàn
    $conflict = DatBan::where('ban_id', $request->ban_id)
        ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
        ->where('gio_den', '<', $request->gio_den)
        ->first();

    if ($conflict) {
        $gio = Carbon::parse($conflict->gio_den)->format('H:i');
        return back()->withInput()->with('error', "Bàn {$banAn->so_ban} đã bị đặt lúc {$gio} với Mã {$conflict->ma_dat_ban}.");
    }

    DB::beginTransaction();
    try {
        $maDatBan = 'DB-' . $now->format('YmdHis') . '-' . strtoupper(Str::random(3));
        $datBan = DatBan::create([
            'ma_dat_ban' => $maDatBan,
            'ten_khach' => $request->ten_khach,
            'email_khach' => $request->email_khach,
            'sdt_khach' => $request->sdt_khach,
            'so_khach' => $request->so_khach,
            'ban_id' => $request->ban_id,
            'combo_id' => $request->combo_id,
            'gio_den' => Carbon::parse($request->gio_den),
            'ghi_chu' => $request->ghi_chu,
            'trang_thai' => 'cho_xac_nhan', // chỉ tạo chờ xác nhận
            'nhan_vien_id' => $request->nhan_vien_id ?? Auth::id(),
            'tien_coc' => 0,
            'la_dat_online' => 0,
        ]);

        // Cập nhật trạng thái bàn
        if (in_array($banAn->trang_thai, ['trong','da_dat'])) {
            $banAn->update(['trang_thai' => 'da_dat']);
        }

        DB::commit();

        return redirect()->route('nhanVien.datban.index')
                         ->with('success', 'Tạo đặt bàn thành công!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Lỗi lưu Đặt bàn NV: " . $e->getMessage());
        return back()->withInput()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
    }
}


    public function thayDoiTrangThai(Request $request, DatBan $datBan)
    {
        $trangThaiMoi = $request->input('trang_thai');
        $message = '';

        $trangThaiHopLe = ['da_xac_nhan', 'huy', 'khach_da_den', 'hoan_tat'];
        if (!in_array($trangThaiMoi, $trangThaiHopLe)) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
        }

        $banAn = $datBan->banAn;
        if (!$banAn) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin bàn ăn.');
        }

        DB::beginTransaction();
        try {
            switch ($trangThaiMoi) {
                case 'da_xac_nhan':
                    if ($datBan->getOriginal('trang_thai') !== 'cho_xac_nhan') {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Không thể xác nhận đơn đặt bàn này.');
                    }
                    $banAn->trang_thai = 'da_dat';
                    $banAn->save();
                    if (empty($datBan->nhan_vien_id)) {
                        $datBan->nhan_vien_id = Auth::id();
                    }
                    $message = 'Đã xác nhận đặt bàn thành công.';
                    break;

case 'khach_da_den':
    if ($datBan->getOriginal('trang_thai') !== 'da_xac_nhan') {
        DB::rollBack();
        return redirect()->back()->with('error', 'Chỉ có thể Check-in khi đơn đã được xác nhận.');
    }

    // Cập nhật trạng thái bàn
    $banAn->trang_thai = 'dang_phuc_vu';
    $banAn->save();

    // ✅ Không tạo OrderMon ở đây
    $message = 'Khách đã đến, bắt đầu phục vụ.';
    break;


                case 'huy':
                    $isTableStillInUse = DatBan::where('ban_id', $datBan->ban_id)
                        ->where('id', '!=', $datBan->id)
                        ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
                        ->exists();
                    if (!$isTableStillInUse && $banAn->trang_thai !== 'khong_su_dung') {
                        $banAn->trang_thai = 'trong';
                        $banAn->save();
                    }
                    $message = 'Đã hủy đơn đặt bàn.';
                    break;

                case 'hoan_tat':
                    if ($datBan->getOriginal('trang_thai') !== 'khach_da_den') {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Chỉ có thể Hoàn tất khi khách đang được phục vụ.');
                    }
                    if ($banAn->trang_thai !== 'khong_su_dung') {
                        $banAn->trang_thai = 'trong';
                        $banAn->save();
                    }
                    $datBan->save();
                    DB::commit();
                    return redirect()->route('nhanVien.hoadon.create', ['dat_ban_id' => $datBan->id])
                                     ->with('success', 'Kết thúc phục vụ. Chuyển sang thanh toán và lập hóa đơn.');
            }

            $datBan->trang_thai = $trangThaiMoi;
            $datBan->save();
            DB::commit();

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi thay đổi trạng thái Đặt bàn: ID={$datBan->id}, Trạng thái mới={$trangThaiMoi}. Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi hệ thống khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }
}