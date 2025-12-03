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
        $nhanViens = NhanVien::where('trang_thai', 1)
            ->whereIn('vai_tro', ['le_tan'])
            ->get();
        return view('shop.nhanvien.datban.create', compact('combos', 'nhanViens'));
    }

    /**
     * AJAX: Kiểm tra các bàn còn trống.
     */
    public function ajaxCheckBanTrong(Request $request)
    {
        $selectedTime = $request->input('time');
        $soKhach = $request->input('so_khach', 1);

        if (!$selectedTime) {
            return response()->json(['error' => 'Vui lòng chọn giờ.'], 400);
        }

        $defaultDuration = 120;
        $newStart = Carbon::parse($selectedTime);
        $newEnd = $newStart->copy()->addMinutes($defaultDuration);

        $conflictingIds = DatBan::whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->where(function ($query) use ($newStart, $newEnd, $defaultDuration) {
                $query->where('gio_den', '<', $newEnd)
                    ->whereRaw(
                        "DATE_ADD(gio_den, INTERVAL IFNULL(thoi_luong_phut, ?) MINUTE) > ?",
                        [$defaultDuration, $newStart]
                    );
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
        $tongCombo = collect($request->input('combos', []))->sum('so_luong');

        if ($tongCombo < $tongKhach) {
            return back()->withInput()->with(
                'error',
                "Bạn có {$tongKhach} khách nhưng chỉ chọn {$tongCombo} combo. Vui lòng chọn ít nhất {$tongKhach} combo."
            );
        }
        $banAn = BanAn::find($request->ban_id);

        if ($banAn->so_ghe < $tongKhach) {
            return back()->withInput()->with('error', "Bàn này chỉ có {$banAn->so_ghe} ghế, không đủ cho {$tongKhach} khách.");
        }

        $thoiLuongPhut = ComboBuffet::whereIn('id', collect($request->combos)->pluck('id'))
            ->max('thoi_luong_phut') ?? 120;

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
            return back()->withInput()->with('error', "Bàn {$banAn->so_ban} đã bị đặt lúc {$gio} với Mã {$conflict->ma_dat_ban}.");
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
                'trang_thai' => 'khach_da_den',
                'nhan_vien_id' => $request->nhan_vien_id ?? Auth::id(),
                'tien_coc' => 0,
                'la_dat_online' => 0,
            ]);

            if (!empty($request->combos)) {
                $combosToAttach = [];
                foreach ($request->combos as $combo) {
                    $combosToAttach[$combo['id']] = ['so_luong' => $combo['so_luong']];
                }
                $datBan->combos()->attach($combosToAttach);
            }

            if ($banAn->trang_thai === 'trong') {
                $banAn->update(['trang_thai' => 'da_dat']);
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

        // --- ĐÃ BỎ LOGIC KIỂM TRA BẮT BUỘC PHẢI CÓ BÀN ---
        // Lấy thông tin bàn nhưng không return lỗi nếu null
        $banAn = $datBan->banAn;

        DB::beginTransaction();
        try {
            switch ($trangThaiMoi) {
                case 'da_xac_nhan':
                    if ($datBan->trang_thai !== 'cho_xac_nhan') {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Chỉ có thể xác nhận đơn đặt bàn ở trạng thái Chờ xác nhận.');
                    }
                    
                    // Chỉ cập nhật trạng thái bàn nếu bàn tồn tại
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
                    if ($datBan->trang_thai !== 'da_xac_nhan') {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Chỉ có thể Check-in khi đơn đã được xác nhận.');
                    }

                    // Chỉ cập nhật trạng thái bàn nếu bàn tồn tại
                    if ($banAn) {
                        $banAn->trang_thai = 'dang_phuc_vu';
                        $banAn->save();
                    }

                    $message = 'Khách đã đến, bắt đầu phục vụ.';
                    break;

                case 'huy':
                    // Chỉ xử lý bàn nếu bàn tồn tại
                    if ($banAn) {
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

                    // Chỉ cập nhật trạng thái bàn nếu bàn tồn tại
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
            Log::error("Lỗi thay đổi trạng thái Đặt bàn: ID={$datBan->id}, Trạng thái mới={$trangThaiMoi}. Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}