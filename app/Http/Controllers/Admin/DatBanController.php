<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\NhanVien;
use App\Models\ChiTietDatBan; // Đảm bảo Model này đã map đúng bảng 'dat_ban_combo'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatBanController extends Controller
{
    /**
     * Danh sách đặt bàn
     */
    public function index(Request $request)
    {
        try {
            $query = DatBan::with(['chiTietDatBan.combo', 'banAn', 'nhanVien']);

            // Tìm kiếm
            if ($request->filled('q')) {
                $keyword = trim($request->q);
                $query->where(function ($q) use ($keyword) {
                    $q->where('ma_dat_ban', 'LIKE', "%$keyword%")
                        ->orWhere('ten_khach', 'LIKE', "%$keyword%")
                        ->orWhere('sdt_khach', 'LIKE', "%$keyword%");
                });
            }
            // Lọc trạng thái
            if ($request->filled('status')) {
                $query->where('trang_thai', $request->status);
            }
            // Lọc theo bàn
            if ($request->filled('ban_id')) {
                $query->where('ban_id', $request->ban_id);
            }
            // Lọc ngày
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

    /**
     * Form tạo mới
     */
    public function create()
    {
        try {
            $banAns = BanAn::where('trang_thai', '!=', 'khong_su_dung')->get();
            
            // Lấy danh sách loại combo để hiển thị ra view
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

    /**
     * Xử lý lưu đặt bàn (Store)
     */
    public function store(Request $request)
    {
        // 1. Validate: Cho phép combos là null
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'email_khach' => 'nullable|email|max:255',
            'nguoi_lon' => 'required|integer|min:1',
            'tre_em'    => 'nullable|integer|min:0',
            'ban_id'    => 'nullable|exists:ban_an,id',
            'gio_den'   => 'required|date',
            'combos'    => 'nullable|array', 
            'combos.*.id' => 'nullable|exists:combo_buffet,id',
            'combos.*.so_luong' => 'nullable|integer|min:0',
        ]);

        $tongNguoi = $request->nguoi_lon + ($request->tre_em ?? 0);
        $hasCombo = false; // Cờ kiểm tra xem khách có chọn combo không
        $tongCombo = 0;
        
        // Tính tổng combo khách chọn
        if ($request->filled('combos')) {
            foreach ($request->combos as $c) {
                $qty = (int)($c['so_luong'] ?? 0);
                if ($qty > 0) {
                    $tongCombo += $qty;
                    $hasCombo = true;
                }
            }
        }

        // 2. Logic: Chỉ chặn nếu CÓ chọn combo mà chọn thiếu suất
        if ($hasCombo && $tongCombo < $tongNguoi) {
            return back()->with('error', "Lỗi: Bạn đã chọn combo nhưng số suất ($tongCombo) ít hơn số người ($tongNguoi). Vui lòng chọn đủ hoặc để trống nếu chưa muốn chọn món.")->withInput();
        }

        // 3. Xử lý thời lượng (Duration)
        $duration = 120; // Mặc định 120 phút nếu không chọn combo
        
        if ($hasCombo) {
            // Lấy thời lượng lớn nhất từ các combo đã chọn
            $comboIds = array_column($request->combos, 'id');
            $dbMaxDuration = ComboBuffet::whereIn('id', $comboIds)->max('thoi_luong_phut');
            if ($dbMaxDuration) {
                $duration = $dbMaxDuration;
            }
        }

        $newStart = Carbon::parse($request->gio_den);

        // 4. Check trùng lịch (Overlap Check)
        if ($request->ban_id) {
            $conflict = DatBan::where('ban_id', $request->ban_id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat']) // Không tính đơn đã hủy/xong
                ->whereBetween('gio_den', [
                    $newStart->copy()->subMinutes($duration - 1),
                    $newStart->copy()->addMinutes($duration - 1)
                ])->first();

            if ($conflict) {
                return back()->with('error', "Bàn đã có khách vào lúc " . Carbon::parse($conflict->gio_den)->format('H:i') . ". Vui lòng chọn bàn khác hoặc giờ khác.")->withInput();
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

            // Chỉ lưu chi tiết combo nếu khách có chọn
            if ($hasCombo) {
                foreach ($request->combos as $item) {
                    if (($item['so_luong'] ?? 0) > 0) {
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

    /**
     * Chi tiết đặt bàn
     */
    public function show($id) {
        try {
            $datBan = DatBan::with(['banAn', 'chiTietDatBan.combo', 'nhanVien'])->findOrFail($id);
            return view('admins.dat-ban.show', compact('datBan'));
        } catch (\Exception $e) {
            return redirect()->route('admin.dat-ban.index')->with('error', 'Không tìm thấy đơn đặt bàn.');
        }
    }

    /**
     * Form chỉnh sửa (Đây là hàm bạn bị thiếu trước đó)
     */
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

            // Logic lấy combo hiện tại để fill vào form
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
            Log::error("Lỗi edit: " . $e->getMessage());
            return redirect()->route('admin.dat-ban.index')->with('error', 'Lỗi tải đơn.');
        }
    }

    /**
     * Cập nhật đặt bàn (Update)
     */
    public function update(Request $request, $id)
    {
        $datBan = DatBan::findOrFail($id);
        
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'email_khach' => 'nullable|email|max:255',
            'nguoi_lon' => 'required|integer|min:1',
            'tre_em'    => 'nullable|integer|min:0',
            'gio_den'   => 'required|date',
            'combos'    => 'nullable|array',
            'combos.*.id' => 'nullable|exists:combo_buffet,id',
            'combos.*.so_luong' => 'nullable|integer|min:0',
        ]);

        $tongNguoi = $request->nguoi_lon + ($request->tre_em ?? 0);
        $hasCombo = false;
        $tongCombo = 0;

        if ($request->filled('combos')) {
            foreach ($request->combos as $c) {
                $qty = (int)($c['so_luong'] ?? 0);
                if ($qty > 0) {
                    $tongCombo += $qty;
                    $hasCombo = true;
                }
            }
        }

        if ($hasCombo && $tongCombo < $tongNguoi) {
            return back()->with('error', "Lỗi: Tổng số suất Combo ($tongCombo) không được ít hơn tổng số người ($tongNguoi).")->withInput();
        }

        // Tính lại thời lượng
        $duration = 120;
        if ($hasCombo) {
            $comboIds = array_column($request->combos, 'id');
            $dbMaxDuration = ComboBuffet::whereIn('id', $comboIds)->max('thoi_luong_phut');
            if ($dbMaxDuration) $duration = $dbMaxDuration;
        }

        $newStart = Carbon::parse($request->gio_den);

        // Check trùng lịch (Trừ chính đơn này ra)
        if ($request->ban_id) {
            $conflict = DatBan::where('ban_id', $request->ban_id)
                ->where('id', '!=', $id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->whereBetween('gio_den', [
                    $newStart->copy()->subMinutes($duration - 1),
                    $newStart->copy()->addMinutes($duration - 1)
                ])->first();

            if ($conflict) {
                return back()->with('error', "Bàn bị trùng giờ với đơn khác.")->withInput();
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
                'thoi_luong_phut' => $duration,
                'tien_coc' => $request->tien_coc ?? 0,
                'ghi_chu' => $request->ghi_chu,
            ]);

            // Xóa hết combo cũ của đơn này và thêm mới (nếu có)
            ChiTietDatBan::where('dat_ban_id', $id)->delete();
            
            if ($hasCombo) {
                foreach ($request->combos as $item) {
                    if (($item['so_luong'] ?? 0) > 0) {
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

    /**
     * Xóa đặt bàn
     */
    public function destroy($id) {
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

    /**
     * AJAX: Cập nhật trạng thái nhanh
     */
    public function updateStatus(Request $request, $id) {
        $datBan = DatBan::findOrFail($id);
        $datBan->update(['trang_thai' => $request->trang_thai_moi]);
        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    /**
     * AJAX: Lấy danh sách bàn trống theo giờ
     */
    public function ajaxGetAvailableTables(Request $request) {
        $selectedTime = $request->input('time');
        $excludeBookingId = $request->input('exclude_booking_id', 0);

        if (!$selectedTime) return response()->json([]);

        // Mặc định check theo 120 phút nếu chưa biết combo
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
            ->with('khuVuc')
            ->get();

        return response()->json($availableTables);
    }

    /**
     * AJAX: Lấy combo theo loại (99k, 199k...)
     */
    public function ajaxGetCombosByLoai(Request $request)
    {
        $loai = $request->input('loai_combo');
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