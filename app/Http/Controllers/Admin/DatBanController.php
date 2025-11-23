<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DatBanController extends Controller
{
    /** ===========================
     * HIỂN THỊ DANH SÁCH ĐẶT BÀN (CÓ LỌC & TÌM KIẾM)
     * =========================== */
    public function index(Request $request)
    {
        try {
            $query = DatBan::with(['comboBuffet', 'banAn', 'nhanVien']);

            // Tìm kiếm theo tên, SĐT, mã đặt bàn
            if ($request->filled('q')) {
                $keyword = trim($request->q);
                $query->where(function ($q) use ($keyword) {
                    $q->where('ma_dat_ban', 'LIKE', "%$keyword%")
                        ->orWhere('ten_khach', 'LIKE', "%$keyword%")
                        ->orWhere('sdt_khach', 'LIKE', "%$keyword%");
                });
            }

            // Lọc theo trạng thái
            if ($request->filled('status')) {
                $query->where('trang_thai', $request->status);
            }

            // Lọc theo combo buffet
            if ($request->filled('combo_id')) {
                $query->where('combo_id', $request->combo_id);
            }

            // Lọc theo bàn ăn
            if ($request->filled('ban_id')) {
                $query->where('ban_id', $request->ban_id);
            }

            // Lọc theo thời gian
            if ($request->filled('date_from')) {
                $query->where('gio_den', '>=', Carbon::parse($request->date_from)->startOfDay());
            }
            if ($request->filled('date_to')) {
                $query->where('gio_den', '<=', Carbon::parse($request->date_to)->endOfDay());
            }

            $danhSachDatBan = $query->orderByDesc('created_at')->paginate(10);
            $combosAll = ComboBuffet::where('trang_thai', 'dang_ban')->get(['id', 'ten_combo']);
            $banAnsAll = BanAn::with('khuVuc')->where('trang_thai', '!=', 'khong_su_dung')->get(['id', 'so_ban', 'khu_vuc_id']);

            return view('admins.dat-ban.index', compact('danhSachDatBan', 'combosAll', 'banAnsAll'));
        } catch (\Exception $e) {
            Log::error("Lỗi khi lấy danh sách đặt bàn: " . $e->getMessage());
            return back()->with('error', 'Không thể tải danh sách đặt bàn.');
        }
    }

    /** Hiển thị form thêm mới đặt bàn */
    public function create()
    {
        try {
            $banAns = BanAn::where('trang_thai', '!=', 'khong_su_dung')->get();
            $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
            return view('admins.dat-ban.create', compact('banAns', 'combos'));
        } catch (\Exception $e) {
            Log::error("Lỗi khi tải form tạo đặt bàn: " . $e->getMessage());
            return back()->with('error', 'Không thể tải form tạo đặt bàn.');
        }
    }

    /** Lưu đơn đặt bàn mới */
    public function store(Request $request)
    {
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'email_khach' => 'required|email|max:255', // <-- thêm validate email
            'sdt_khach' => 'required|string|max:20',
            'so_khach' => 'required|integer|min:1',
            'ban_id' => 'required|exists:ban_an,id',
            'combo_id' => 'nullable|exists:combo_buffet,id',
            'gio_den' => 'required|date',
            'tien_coc' => 'nullable|numeric|min:0',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => ['nullable', Rule::in(['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])],
        ]);

        $duration = 120; // Thời lượng mặc định 2 tiếng
        $newStart = Carbon::parse($request->gio_den);
        $newEnd = $newStart->copy()->addMinutes($duration);
        $now = Carbon::now();

        $banAn = BanAn::find($request->ban_id);

        if (!$banAn || $banAn->trang_thai === 'khong_su_dung') {
            return back()->with('error', 'Bàn này đang tạm ngưng phục vụ (Hỏng/Bảo trì). Vui lòng chọn bàn khác.');
        }

        if ($banAn->trang_thai === 'dang_phuc_vu') {
            $duKienKhachCuXong = $now->copy()->addMinutes($duration);
            if ($newStart < $duKienKhachCuXong) {
                 return back()->with('error', 'Bàn này HIỆN TẠI đang có khách ăn. Không thể đặt vào giờ này.');
            }
        }

        $conflict = DatBan::where('ban_id', $request->ban_id)
            ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->where('gio_den', '<', $newEnd)
                      ->whereRaw("DATE_ADD(gio_den, INTERVAL thoi_luong_phut MINUTE) > ?", [$newStart]);
            })
            ->first();

        if ($conflict) {
            $gioBiTrung = Carbon::parse($conflict->gio_den)->format('H:i d/m/Y');
            return back()->with('error', "Bàn này đã bị đặt trước vào lúc $gioBiTrung. Vui lòng chọn giờ khác.");
        }

        DB::beginTransaction();
        try {
            $maDatBan = 'DB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            $trangThai = $request->trang_thai ?? 'cho_xac_nhan';

            $nhanVienId = null;
            if ($trangThai === 'khach_da_den') {
                $nhanVien = NhanVien::where('trang_thai', 1)
                    ->where('vai_tro', 'Phục vụ')
                    ->inRandomOrder()
                    ->first();
                $nhanVienId = $nhanVien ? $nhanVien->id : null;
            }

            DatBan::create([
                'ma_dat_ban' => $maDatBan,
                'ten_khach' => $request->ten_khach,
                'email_khach' => $request->email_khach, // <-- thêm email_khach
                'sdt_khach' => $request->sdt_khach,
                'so_khach' => $request->so_khach,
                'ban_id' => $request->ban_id,
                'combo_id' => $request->combo_id,
                'gio_den' => $request->gio_den,
                'thoi_luong_phut' => $duration,
                'tien_coc' => $request->tien_coc ?? 0,
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => $trangThai,
                'la_dat_online' => 0,
                'nhan_vien_id' => $nhanVienId
            ]);

            if ($trangThai === 'khach_da_den') {
                $banAn->trang_thai = 'dang_phuc_vu';
                $banAn->save();
            } 
            elseif ($trangThai === 'da_xac_nhan') {
                $limitTime = $now->copy()->addMinutes(30);
                if ($newStart >= $now && $newStart <= $limitTime && $banAn->trang_thai === 'trong') {
                    $banAn->trang_thai = 'da_dat';
                    $banAn->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.dat-ban.index')->with('success', "Tạo đơn thành công! Mã: $maDatBan");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi lưu đặt bàn: " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống, không thể tạo đơn.');
        }
    }

    /** Xem chi tiết đặt bàn */
    public function show($id)
    {
        try {
            $datBan = DatBan::with(['banAn', 'comboBuffet', 'nhanVien'])->findOrFail($id);
            return view('admins.dat-ban.show', compact('datBan'));
        } catch (\Exception $e) {
            Log::error("Lỗi khi xem chi tiết đặt bàn: " . $e->getMessage());
            return redirect()->route('admin.dat-ban.index')->with('error', 'Không tìm thấy đơn đặt bàn.');
        }
    }

    /** Hiển thị form sửa đặt bàn */
    public function edit($id)
    {
        try {
            $datBan = DatBan::findOrFail($id);
            if (in_array($datBan->trang_thai, ['hoan_tat', 'huy'])) {
                return redirect()->route('admin.dat-ban.index')->with('error', 'Không thể sửa đơn đã hoàn tất hoặc hủy.');
            }

            $banAns = BanAn::where('trang_thai', '!=', 'khong_su_dung')->get();
            $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();

            return view('admins.dat-ban.edit', compact('datBan', 'banAns', 'combos'));
        } catch (\Exception $e) {
            Log::error("Lỗi khi tải form sửa: " . $e->getMessage());
            return redirect()->route('admin.dat-ban.index')->with('error', 'Không thể tải đơn đặt bàn.');
        }
    }

    /** Cập nhật đơn đặt bàn */
    public function update(Request $request, $id)
    {
        $datBan = DatBan::findOrFail($id);
        if (in_array($datBan->trang_thai, ['hoan_tat', 'huy'])) {
            return back()->with('error', 'Không thể sửa đơn đã hoàn tất hoặc hủy.');
        }

        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'email_khach' => 'required|email|max:255', // <-- thêm validate email
            'sdt_khach' => 'required|string|max:20',
            'so_khach' => 'required|integer|min:1',
            'ban_id' => 'required|exists:ban_an,id',
            'combo_id' => 'nullable|exists:combo_buffet,id',
            'gio_den' => 'required|date',
            'tien_coc' => 'nullable|numeric|min:0',
            'ghi_chu' => 'nullable|string',
        ]);

        $duration = 120;
        $newStart = Carbon::parse($request->gio_den);

        $conflict = DatBan::where('ban_id', $request->ban_id)
            ->where('id', '!=', $id)
            ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->whereBetween('gio_den', [
                $newStart->copy()->subMinutes($duration - 1),
                $newStart->copy()->addMinutes($duration - 1)
            ])
            ->first();

        if ($conflict) {
            $gioBiTrung = Carbon::parse($conflict->gio_den)->format('H:i d/m/Y');
            return back()->with('error', "Bàn này đã được đặt vào lúc $gioBiTrung.");
        }

        try {
            $datBan->update([
                'ten_khach' => $request->ten_khach,
                'email_khach' => $request->email_khach, // <-- thêm email_khach
                'sdt_khach' => $request->sdt_khach,
                'so_khach' => $request->so_khach,
                'ban_id' => $request->ban_id,
                'combo_id' => $request->combo_id,
                'gio_den' => $request->gio_den,
                'ghi_chu' => $request->ghi_chu,
                'tien_coc' => $request->tien_coc ?? 0,
                'thoi_luong_phut' => $duration,
            ]);

            return redirect()->route('admin.dat-ban.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi khi cập nhật: " . $e->getMessage());
            return back()->with('error', 'Không thể cập nhật đặt bàn.');
        }
    }

    /** Xóa đặt bàn */
    public function destroy($id)
    {
        try {
            $datBan = DatBan::findOrFail($id);
            if ($datBan->trang_thai == 'khach_da_den') {
                return back()->with('error', 'Không thể xóa khi khách đang ăn.');
            }

            $banAn = $datBan->banAn;
            if ($banAn && in_array($datBan->trang_thai, ['da_xac_nhan', 'cho_xac_nhan'])) {
                $other = DatBan::where('ban_id', $banAn->id)
                    ->where('id', '!=', $datBan->id)
                    ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
                    ->exists();
                if (!$other) {
                    $banAn->update(['trang_thai' => 'trong']);
                }
            }

            $datBan->delete();
            return redirect()->route('admin.dat-ban.index')->with('success', 'Xóa thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi xóa đặt bàn: " . $e->getMessage());
            return back()->with('error', 'Không thể xóa đặt bàn.');
        }
    }

    /** =================================================
     * CẬP NHẬT TRẠNG THÁI ĐẶT BÀN & TRẠNG THÁI BÀN ĂN
     * ================================================= */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai_moi' => ['required', Rule::in(['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den', 'hoan_tat', 'huy'])]
        ]);

        DB::beginTransaction();

        try {
            $datBan = DatBan::findOrFail($id);
            $newStatus = $request->trang_thai_moi;
            $banAn = BanAn::find($datBan->ban_id);

            if ($newStatus === 'khach_da_den' && !$datBan->nhan_vien_id) {
                $nhanVien = NhanVien::where('trang_thai', 1)
                    ->where('vai_tro', 'Phục vụ')
                    ->inRandomOrder()
                    ->first();
                if ($nhanVien) {
                    $datBan->nhan_vien_id = $nhanVien->id;
                }
            }

            $datBan->trang_thai = $newStatus;
            $datBan->save();

            if ($banAn) {
                if ($newStatus === 'khach_da_den') {
                    $banAn->trang_thai = 'dang_phuc_vu';
                    $banAn->save();
                } elseif ($newStatus === 'da_xac_nhan') {
                    if ($banAn->trang_thai === 'trong') {
                        $banAn->trang_thai = 'da_dat';
                        $banAn->save();
                    }
                } elseif (in_array($newStatus, ['hoan_tat', 'huy'])) {
                    $isServingOthers = DatBan::where('ban_id', $banAn->id)
                        ->where('id', '!=', $datBan->id)
                        ->where('trang_thai', 'khach_da_den')
                        ->exists();

                    if ($isServingOthers) {
                        $banAn->trang_thai = 'dang_phuc_vu';
                    } else {
                        $isReserved = DatBan::where('ban_id', $banAn->id)
                            ->where('id', '!=', $datBan->id)
                            ->where('trang_thai', 'da_xac_nhan')
                            ->exists();
                        $banAn->trang_thai = $isReserved ? 'da_dat' : 'trong';
                    }
                    $banAn->save();
                }
            }

            DB::commit();
            return back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi cập nhật trạng thái: " . $e->getMessage());
            return back()->with('error', 'Không thể cập nhật trạng thái. Lỗi hệ thống.');
        }
    }

    /** AJAX: lấy danh sách bàn trống theo giờ */
    public function ajaxGetAvailableTables(Request $request)
    {
        $selectedTime = $request->input('time');
        $excludeBookingId = $request->input('exclude_booking_id', 0);

        if (!$selectedTime) {
            return response()->json(['error' => 'Vui lòng chọn giờ.'], 400);
        }

        $duration = 120;
        $newStart = Carbon::parse($selectedTime);

        $conflictingIds = DatBan::whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->whereBetween('gio_den', [
                $newStart->copy()->subMinutes($duration - 1),
                $newStart->copy()->addMinutes($duration - 1)
            ])
            ->when($excludeBookingId > 0, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->pluck('ban_id')
            ->toArray();

        $availableTables = BanAn::where('trang_thai', '!=', 'khong_su_dung')
            ->whereNotIn('id', $conflictingIds)
            ->get();

        return response()->json($availableTables);
    }
}
