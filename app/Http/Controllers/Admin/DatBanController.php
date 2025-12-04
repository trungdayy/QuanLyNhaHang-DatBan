<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\NhanVien;
use App\Models\ChiTietDatBan; // Lưu ý: Đảm bảo Model này map đúng với bảng 'dat_ban_combo' trong SQL
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DatBanController extends Controller
{
    // ... (Giữ nguyên hàm index) ...
    public function index(Request $request)
    {
        try {
            $query = DatBan::with(['chiTietDatBan.combo', 'banAn', 'nhanVien']);

            if ($request->filled('q')) {
                $keyword = trim($request->q);
                $query->where(function ($q) use ($keyword) {
                    $q->where('ma_dat_ban', 'LIKE', "%$keyword%")
                        ->orWhere('ten_khach', 'LIKE', "%$keyword%")
                        ->orWhere('sdt_khach', 'LIKE', "%$keyword%");
                });
            }
            if ($request->filled('status')) {
                $query->where('trang_thai', $request->status);
            }
            if ($request->filled('ban_id')) {
                $query->where('ban_id', $request->ban_id);
            }
            if ($request->filled('date_from')) {
                $query->where('gio_den', '>=', Carbon::parse($request->date_from)->startOfDay());
            }
            if ($request->filled('date_to')) {
                $query->where('gio_den', '<=', Carbon::parse($request->date_to)->endOfDay());
            }

            $danhSachDatBan = $query->orderByDesc('created_at')->paginate(10);
            $banAnsAll = BanAn::with('khuVuc')->where('trang_thai', '!=', 'khong_su_dung')->get(['id', 'so_ban', 'khu_vuc_id']);

            return view('admins.dat-ban.index', compact('danhSachDatBan', 'banAnsAll'));
        } catch (\Exception $e) {
            Log::error("Lỗi index: " . $e->getMessage());
            return back()->with('error', 'Không thể tải danh sách.');
        }
    }

    // ... (Giữ nguyên hàm create) ...
    public function create()
    {
        try {
            $banAns = BanAn::where('trang_thai', '!=', 'khong_su_dung')->get();
            
            // Lấy danh sách LOẠI COMBO duy nhất
            $loaiCombos = ComboBuffet::where('trang_thai', 'dang_ban')
                ->select('loai_combo', 'gia_co_ban')
                ->groupBy('loai_combo', 'gia_co_ban')
                ->orderBy('gia_co_ban', 'asc')
                ->get();

            return view('admins.dat-ban.create', compact('banAns', 'loaiCombos'));
        } catch (\Exception $e) {
            Log::error("Lỗi create: " . $e->getMessage());
            return back()->with('error', 'Lỗi tải form.');
        }
    }

    /** ===========================
     * 3. LƯU DATA (STORE) - ĐÃ BỔ SUNG VALIDATE
     * =========================== */
    public function store(Request $request)
    {
        // 1. Validate cơ bản
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'nguoi_lon' => 'required|integer|min:1',
            'tre_em'    => 'nullable|integer|min:0',
            'ban_id'    => 'nullable|exists:ban_an,id',
            'gio_den'   => 'required|date',
            'combos'    => 'nullable|array', 
            'combos.*.id' => 'required|exists:combo_buffet,id',
            'combos.*.so_luong' => 'required|integer|min:0',
        ]);

        // 2. [QUAN TRỌNG] Validate Logic: Tổng Combo >= Tổng Người
        $tongNguoi = $request->nguoi_lon + ($request->tre_em ?? 0);
        $tongCombo = 0;
        if ($request->filled('combos')) {
            foreach ($request->combos as $c) {
                $tongCombo += (int)$c['so_luong'];
            }
        }

        // Nếu tổng combo = 0 (chưa chọn) hoặc nhỏ hơn số người -> Chặn luôn
        if ($tongCombo < $tongNguoi) {
            return back()->with('error', "Lỗi: Tổng số suất Combo ($tongCombo) không được ít hơn tổng số người ($tongNguoi).")->withInput();
        }

        $duration = 120;
        $newStart = Carbon::parse($request->gio_den);

        // 3. Check trùng lịch
        if ($request->ban_id) {
            $conflict = DatBan::where('ban_id', $request->ban_id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->whereBetween('gio_den', [
                    $newStart->copy()->subMinutes($duration - 1),
                    $newStart->copy()->addMinutes($duration - 1)
                ])->first();

            if ($conflict) {
                return back()->with('error', "Bàn đã có khách lúc " . Carbon::parse($conflict->gio_den)->format('H:i'))->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $maDatBan = 'DB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

            $datBan = DatBan::create([
                'ma_dat_ban' => $maDatBan,
                'ten_khach' => $request->ten_khach,
                'sdt_khach' => $request->sdt_khach,
                'email_khach' => $request->email_khach,
                'nguoi_lon' => $request->nguoi_lon,
                'tre_em'    => $request->tre_em ?? 0,
                'ban_id' => $request->ban_id,
                'gio_den' => $request->gio_den,
                'thoi_luong_phut' => $duration,
                'tien_coc' => $request->tien_coc ?? 0,
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => 'cho_xac_nhan',
                'la_dat_online' => 0,
            ]);

            // Lưu Combo
            if ($request->filled('combos')) {
                foreach ($request->combos as $item) {
                    if ($item['so_luong'] > 0) { // Chỉ lưu những combo có số lượng > 0
                        ChiTietDatBan::create([
                            'dat_ban_id' => $datBan->id,
                            'combo_id'   => $item['id'],
                            'so_luong'   => $item['so_luong'],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.dat-ban.index')->with('success', "Đặt bàn thành công!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi store: " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    // ... (Giữ nguyên hàm edit, show, destroy, updateStatus, ajaxGetAvailableTables) ...
    public function show($id) {
        try {
            $datBan = DatBan::with(['banAn', 'chiTietDatBan.combo', 'nhanVien'])->findOrFail($id);
            return view('admins.dat-ban.show', compact('datBan'));
        } catch (\Exception $e) {
            return redirect()->route('admin.dat-ban.index')->with('error', 'Không tìm thấy đơn đặt bàn.');
        }
    }

    public function edit($id)
    {
        try {
            $datBan = DatBan::with('chiTietDatBan.combo')->findOrFail($id);
            if (in_array($datBan->trang_thai, ['hoan_tat', 'huy'])) {
                return redirect()->route('admin.dat-ban.index')->with('error', 'Không thể sửa đơn đã đóng.');
            }

            $banAns = BanAn::where('trang_thai', '!=', 'khong_su_dung')->get();

            $loaiCombos = ComboBuffet::where('trang_thai', 'dang_ban')
                ->select('loai_combo', 'gia_co_ban')
                ->groupBy('loai_combo', 'gia_co_ban')
                ->orderBy('gia_co_ban', 'asc')
                ->get();

            $currentLoaiCombo = null;
            if ($datBan->chiTietDatBan->count() > 0) {
                $firstDetail = $datBan->chiTietDatBan->first();
                if ($firstDetail && $firstDetail->combo) {
                    $currentLoaiCombo = $firstDetail->combo->loai_combo;
                }
            }

            $combosOfCurrentType = [];
            if ($currentLoaiCombo) {
                $combosOfCurrentType = ComboBuffet::where('loai_combo', $currentLoaiCombo)
                    ->where('trang_thai', 'dang_ban')
                    ->get();
            }

            return view('admins.dat-ban.edit', compact('datBan', 'banAns', 'loaiCombos', 'currentLoaiCombo', 'combosOfCurrentType'));
        } catch (\Exception $e) {
            return redirect()->route('admin.dat-ban.index')->with('error', 'Lỗi tải đơn.');
        }
    }

    public function update(Request $request, $id)
    {
        $datBan = DatBan::findOrFail($id);
        
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'nguoi_lon' => 'required|integer|min:1',
            'gio_den'   => 'required|date',
            'combos'    => 'nullable|array',
            'combos.*.id' => 'required|exists:combo_buffet,id',
            'combos.*.so_luong' => 'required|integer|min:0',
        ]);

        // Validate Tổng Combo Backend
        $tongNguoi = $request->nguoi_lon + ($request->tre_em ?? 0);
        $tongCombo = 0;
        if ($request->filled('combos')) {
            foreach ($request->combos as $c) {
                $tongCombo += (int)$c['so_luong'];
            }
        }
        if ($tongCombo < $tongNguoi) {
            return back()->with('error', "Lỗi: Tổng số suất Combo ($tongCombo) không được ít hơn tổng số người ($tongNguoi).")->withInput();
        }

        $duration = 120;
        $newStart = Carbon::parse($request->gio_den);

        if ($request->ban_id) {
            $conflict = DatBan::where('ban_id', $request->ban_id)
                ->where('id', '!=', $id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->whereBetween('gio_den', [
                    $newStart->copy()->subMinutes($duration - 1),
                    $newStart->copy()->addMinutes($duration - 1)
                ])->first();

            if ($conflict) {
                return back()->with('error', "Bàn bị trùng giờ.")->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $datBan->update([
                'ten_khach' => $request->ten_khach,
                'sdt_khach' => $request->sdt_khach,
                'email_khach' => $request->email_khach,
                'nguoi_lon' => $request->nguoi_lon,
                'tre_em'    => $request->tre_em ?? 0,
                'ban_id' => $request->ban_id,
                'gio_den' => $request->gio_den,
                'tien_coc' => $request->tien_coc ?? 0,
                'ghi_chu' => $request->ghi_chu,
            ]);

            ChiTietDatBan::where('dat_ban_id', $id)->delete();
            if ($request->filled('combos')) {
                foreach ($request->combos as $item) {
                    if ($item['so_luong'] > 0) {
                        ChiTietDatBan::create([
                            'dat_ban_id' => $datBan->id,
                            'combo_id'   => $item['id'],
                            'so_luong'   => $item['so_luong'],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.dat-ban.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi update: " . $e->getMessage());
            return back()->with('error', 'Lỗi cập nhật.');
        }
    }

    public function destroy($id) {
        // ... (Code cũ của bạn) ...
        try {
            $datBan = DatBan::findOrFail($id);
            if ($datBan->trang_thai == 'khach_da_den') {
                return back()->with('error', 'Không thể xóa khi khách đang ăn.');
            }
            $datBan->delete();
            return redirect()->route('admin.dat-ban.index')->with('success', 'Xóa thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa.');
        }
    }

    public function updateStatus(Request $request, $id) {
        // ... (Code cũ của bạn) ...
        // Nhớ validate và update
        $datBan = DatBan::findOrFail($id);
        $datBan->update(['trang_thai' => $request->trang_thai_moi]);
        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function ajaxGetAvailableTables(Request $request) {
        $selectedTime = $request->input('time');
        $excludeBookingId = $request->input('exclude_booking_id', 0);

        if (!$selectedTime) return response()->json([]);

        $duration = 120;
        $newStart = Carbon::parse($selectedTime);

        $conflictingIds = DatBan::whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->whereNotNull('ban_id')
            ->whereBetween('gio_den', [
                $newStart->copy()->subMinutes($duration - 1),
                $newStart->copy()->addMinutes($duration - 1)
            ])
            ->when($excludeBookingId > 0, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->pluck('ban_id')->toArray();

        $availableTables = BanAn::where('trang_thai', '!=', 'khong_su_dung')
            ->whereNotIn('id', $conflictingIds)
            ->with('khuVuc') // Eager load khu vực để hiển thị tên
            ->get();

        return response()->json($availableTables);
    }

    /** ===========================
     * [FIX LỖI] AJAX: LẤY DANH SÁCH COMBO
     * =========================== */
    public function ajaxGetCombosByLoai(Request $request)
    {
        $loai = $request->input('loai_combo');
        
        // [FIX] Luôn trả về JSON rỗng thay vì HTML để JS không bị lỗi
        if (!$loai) {
            return response()->json(['combos' => []]);
        }

        $combos = ComboBuffet::where('loai_combo', $loai)
            ->where('trang_thai', 'dang_ban')
            ->get(['id', 'ten_combo', 'gia_co_ban']);

        return response()->json([
            'combos' => $combos
        ]);
    }
}