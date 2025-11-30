<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\NhanVien;
use App\Models\ChiTietDatBan; // [MỚI] Model bảng trung gian
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // [MỚI] Để dùng Transaction

class DatBanController extends Controller
{
    /** ===========================
     * HIỂN THỊ DANH SÁCH ĐẶT BÀN (CÓ LỌC & TÌM KIẾM)
     * =========================== */
    public function index(Request $request)
    {
        try {
            // [SỬA] Eager load quan hệ mới: chiTietDatBan.combo
            $query = DatBan::with(['chiTietDatBan.combo', 'banAn', 'nhanVien']);

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

            // [SỬA] Lọc theo combo buffet (Logic mới: Dùng whereHas truy vấn bảng con)
            if ($request->filled('combo_id')) {
                $query->whereHas('chiTietDatBan', function ($q) use ($request) {
                    $q->where('combo_id', $request->combo_id);
                });
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
        // 1. Validate
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'nguoi_lon' => 'required|integer|min:1',
            'tre_em'    => 'nullable|integer|min:0',
            'email_khach' => 'nullable|email|max:255',
            'ban_id'    => 'nullable|exists:ban_an,id',
            'gio_den'   => 'required|date',
            'tien_coc'  => 'nullable|numeric|min:0',
            'ghi_chu'   => 'nullable|string',

            // [MỚI] Validate mảng combos
            'combos'    => 'nullable|array', 
            'combos.*.id' => 'required|exists:combo_buffet,id',
            'combos.*.so_luong' => 'required|integer|min:1',
        ]);

        $duration = 120; // 120 phút mặc định
        $newStart = Carbon::parse($request->gio_den);

        // 2. Kiểm tra trùng lịch (Nếu có chọn bàn)
        if ($request->ban_id) {
            $conflict = DatBan::where('ban_id', $request->ban_id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->whereBetween('gio_den', [
                    $newStart->copy()->subMinutes($duration - 1),
                    $newStart->copy()->addMinutes($duration - 1)
                ])
                ->first();

            if ($conflict) {
                $gioBiTrung = Carbon::parse($conflict->gio_den)->format('H:i d/m/Y');
                return back()->with('error', "Bàn này đã được đặt vào lúc $gioBiTrung. Vui lòng chọn giờ khác hoặc bàn khác.")->withInput();
            }
        }

        // 3. Xử lý lưu DB (Dùng Transaction)
        DB::beginTransaction();
        try {
            $maDatBan = 'DB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

            // A. Tạo DatBan
            $datBan = DatBan::create([
                'ma_dat_ban' => $maDatBan,
                'ten_khach' => $request->ten_khach,
                'sdt_khach' => $request->sdt_khach,
                'nguoi_lon' => $request->nguoi_lon,
                'tre_em'    => $request->tre_em ?? 0,
                'email_khach' => $request->email_khach,
                'ban_id' => $request->ban_id,
                // 'combo_id' => null, // [QUAN TRỌNG] Đã bỏ cột này
                'gio_den' => $request->gio_den,
                'thoi_luong_phut' => $duration,
                'tien_coc' => $request->tien_coc ?? 0,
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => 'cho_xac_nhan',
                'la_dat_online' => 0,
            ]);

            // B. Lưu Chi Tiết Combo (Nếu có chọn)
            if ($request->filled('combos')) {
                foreach ($request->combos as $item) {
                    // Logic tính giá tiền nếu cần, ở đây chỉ lưu số lượng theo yêu cầu của bạn
                    ChiTietDatBan::create([
                        'dat_ban_id' => $datBan->id,
                        'combo_id'   => $item['id'],
                        'so_luong'   => $item['so_luong'],
                        // 'don_gia' => ..., // Đã bỏ theo yêu cầu
                    ]);
                }
            }

            DB::commit(); // Lưu tất cả thành công
            return redirect()->route('admin.dat-ban.index')->with('success', "Tạo đơn đặt bàn thành công! Mã: $maDatBan");

        } catch (\Exception $e) {
            DB::rollBack(); // Có lỗi thì hoàn tác
            Log::error("Lỗi khi lưu đặt bàn: " . $e->getMessage());
            return back()->with('error', 'Không thể lưu đặt bàn.')->withInput();
        }
    }

    /** Xem chi tiết đặt bàn */
    public function show($id)
    {
        try {
            // [SỬA] Load quan hệ chiTietDatBan
            $datBan = DatBan::with(['banAn', 'chiTietDatBan.combo', 'nhanVien'])->findOrFail($id);
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
            // Load kèm chi tiết để fill vào form
            $datBan = DatBan::with('chiTietDatBan')->findOrFail($id);
            
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

        // 1. Validate
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'nguoi_lon' => 'required|integer|min:1',
            'tre_em'    => 'nullable|integer|min:0',
            'email_khach' => 'nullable|email|max:255',
            'ban_id'    => 'nullable|exists:ban_an,id',
            'gio_den'   => 'required|date',
            'tien_coc'  => 'nullable|numeric|min:0',
            'ghi_chu'   => 'nullable|string',
            
            // [MỚI] Validate mảng combos
            'combos'    => 'nullable|array',
            'combos.*.id' => 'required|exists:combo_buffet,id',
            'combos.*.so_luong' => 'required|integer|min:1',
        ]);

        $duration = 120;
        $newStart = Carbon::parse($request->gio_den);

        // 2. Check trùng lịch
        if ($request->ban_id) {
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
        }

        // 3. Update DB (Transaction)
        DB::beginTransaction();
        try {
            // A. Update thông tin chung
            $datBan->update([
                'ten_khach' => $request->ten_khach,
                'sdt_khach' => $request->sdt_khach,
                'nguoi_lon' => $request->nguoi_lon,
                'tre_em'    => $request->tre_em ?? 0,
                'email_khach' => $request->email_khach,
                'ban_id' => $request->ban_id,
                // 'combo_id' => ... // Bỏ
                'gio_den' => $request->gio_den,
                'ghi_chu' => $request->ghi_chu,
                'tien_coc' => $request->tien_coc ?? 0,
                'thoi_luong_phut' => $duration,
            ]);

            // B. Sync Combos (Xóa cũ thêm mới)
            // Xóa hết chi tiết cũ của đơn này
            ChiTietDatBan::where('dat_ban_id', $id)->delete();

            // Thêm lại combo mới chọn
            if ($request->filled('combos')) {
                foreach ($request->combos as $item) {
                    ChiTietDatBan::create([
                        'dat_ban_id' => $datBan->id,
                        'combo_id'   => $item['id'],
                        'so_luong'   => $item['so_luong'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.dat-ban.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
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

            // Kiểm tra trạng thái bàn để mở lại bàn
            $banAn = $datBan->banAn;
            if ($banAn && in_array($datBan->trang_thai, ['da_xac_nhan', 'cho_xac_nhan'])) {
                // Kiểm tra xem bàn này có đơn nào khác đang chờ không
                $other = DatBan::where('ban_id', $banAn->id)
                    ->where('id', '!=', $datBan->id)
                    ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
                    ->exists();
                if (!$other) {
                    $banAn->update(['trang_thai' => 'trong']);
                }
            }

            // ChiTietDatBan sẽ tự xóa theo nếu thiết lập cascade ở DB, 
            // nhưng DatBan::delete() vẫn hoạt động bình thường.
            $datBan->delete();
            return redirect()->route('admin.dat-ban.index')->with('success', 'Xóa thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi xóa đặt bàn: " . $e->getMessage());
            return back()->with('error', 'Không thể xóa đặt bàn.');
        }
    }

    /** Cập nhật trạng thái đặt bàn */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai_moi' => ['required', Rule::in(['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den', 'hoan_tat', 'huy'])]
        ]);

        try {
            $datBan = DatBan::findOrFail($id);
            $newStatus = $request->trang_thai_moi;

            // Gán nhân viên ngẫu nhiên khi khách đến
            if ($newStatus === 'khach_da_den') {
                $nhanVien = NhanVien::where('trang_thai', 1)
                    ->where('vai_tro', 'phuc_vu') 
                    ->inRandomOrder()
                    ->first();
                if ($nhanVien) $datBan->nhan_vien_id = $nhanVien->id;
            }

            $datBan->update(['trang_thai' => $newStatus]);

            // Cập nhật trạng thái bàn ăn
            $banAn = $datBan->banAn;
            if ($banAn) {
                if ($newStatus === 'khach_da_den') {
                    $banAn->update(['trang_thai' => 'dang_phuc_vu']);
                } elseif (in_array($newStatus, ['hoan_tat', 'huy'])) {
                    $hasOther = DatBan::where('ban_id', $banAn->id)
                        ->where('id', '!=', $datBan->id)
                        ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
                        ->exists();
                    if (!$hasOther) $banAn->update(['trang_thai' => 'trong']);
                }
            }

            return back()->with('success', 'Cập nhật trạng thái thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi cập nhật trạng thái: " . $e->getMessage());
            return back()->with('error', 'Không thể cập nhật trạng thái.');
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

        // Tìm các bàn bận
        $conflictingIds = DatBan::whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->whereNotNull('ban_id')
            ->whereBetween('gio_den', [
                $newStart->copy()->subMinutes($duration - 1),
                $newStart->copy()->addMinutes($duration - 1)
            ])
            ->when($excludeBookingId > 0, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->pluck('ban_id')
            ->toArray();

        // Lấy danh sách bàn trống
        $availableTables = BanAn::where('trang_thai', '!=', 'khong_su_dung')
            ->whereNotIn('id', $conflictingIds)
            ->get();

        return response()->json($availableTables);
    }
}