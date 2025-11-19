<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanAn;
use App\Models\KhuVuc;
use App\Models\DatBan; // Import Model DatBan
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Import Carbon

class BanAnController extends Controller
{
    /**
     * Hiển thị danh sách bàn (Sơ đồ bàn) - Logic Realtime
     */
    public function index(Request $request)
    {
        try {
            // 1. Lấy danh sách bàn và khu vực
            $query = BanAn::with('khuVuc');

            // Lọc theo khu vực nếu có
            if ($request->filled('khu_vuc_id')) {
                $query->where('khu_vuc_id', $request->khu_vuc_id);
            }

            // Lấy danh sách bàn (sắp xếp theo tên)
            $banAns = $query->orderBy('so_ban', 'asc')->get();
            $khuVucs = KhuVuc::orderBy('tang')->get();

            // =================================================================
            // 💡 LOGIC TÍNH TOÁN TRẠNG THÁI THỰC TẾ (QUAN TRỌNG)
            // =================================================================
            $timezone = 'Asia/Ho_Chi_Minh';
            $now = Carbon::now($timezone);
            $limitTime = $now->copy()->addMinutes(30); // Check đơn đặt trong 30 phút tới

            // Duyệt qua từng bàn để tính toán lại trạng thái hiển thị
            $banAns->transform(function ($ban) use ($now, $limitTime) {
                
                // 1. Nếu bàn đang Hỏng/Ngưng sử dụng -> Giữ nguyên, không xử lý
                if ($ban->trang_thai === 'khong_su_dung') {
                    return $ban;
                }

                // --- BẮT ĐẦU TÍNH TOÁN TRẠNG THÁI ---
                $trangThaiMoi = 'trong'; // Mặc định coi như là Trống
                $thongTinBooking = null;

                // Bước 1: Kiểm tra xem có khách đang ngồi ăn không? (Ưu tiên cao nhất)
                // Điều kiện: Có đơn 'khach_da_den' tại bàn này
                $dangPhucVu = DatBan::where('ban_id', $ban->id)
                    ->where('trang_thai', 'khach_da_den')
                    ->first();

                if ($dangPhucVu) {
                    $trangThaiMoi = 'dang_phuc_vu';
                    $thongTinBooking = "Đang phục vụ: {$dangPhucVu->ten_khach}";
                } 
                else {
                    // Bước 2: Nếu không ai ăn, kiểm tra xem có khách đặt sắp tới không?
                    // Điều kiện: Có đơn 'da_xac_nhan' VÀ (Giờ đến >= Hiện tại HOẶC trễ hẹn không quá 60p)
                    // VÀ (Giờ đến <= Hiện tại + 30p)
                    $sapToi = DatBan::where('ban_id', $ban->id)
                        ->where('trang_thai', 'da_xac_nhan')
                        ->where(function($q) use ($now, $limitTime) {
                            $q->whereBetween('gio_den', [$now, $limitTime]) // Sắp đến trong 30p
                              ->orWhere(function($sub) use ($now) {
                                  $sub->where('gio_den', '<', $now) 
                                      ->where('gio_den', '>', $now->copy()->subMinutes(60)); // Trễ hẹn < 60p
                              });
                        })
                        ->orderBy('gio_den', 'asc')
                        ->first();

                    if ($sapToi) {
                        $trangThaiMoi = 'da_dat';
                        $gioDen = Carbon::parse($sapToi->gio_den)->format('H:i');
                        $thongTinBooking = "Khách: {$sapToi->ten_khach} ({$gioDen})";
                    }
                }

                // --- BƯỚC QUAN TRỌNG: TỰ ĐỘNG SỬA LỖI DATA (SELF-HEALING) ---
                // Nếu trạng thái tính toán được KHÁC với trạng thái đang lưu trong DB
                // => Cập nhật lại DB ngay lập tức để đồng bộ
                if ($ban->trang_thai !== $trangThaiMoi) {
                    $ban->trang_thai = $trangThaiMoi;
                    $ban->save();
                }

                // Gán thông tin bổ sung để hiển thị ra View (nếu cần tooltip)
                $ban->booking_info = $thongTinBooking;

                return $ban;
            });
            // =================================================================

            return view('admins.khu-vuc-ban-an', compact('banAns', 'khuVucs'));

        } catch (\Exception $e) {
            Log::error("Lỗi tải danh sách bàn: " . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi tải danh sách bàn.');
        }
    }

    /**
     * Hiển thị Form Tạo Bàn Ăn Mới.
     */
    public function create()
    {
        $khuVucs = KhuVuc::orderBy('tang')->get();
        return view('admins.ban-an.create', ['khuVucs' => $khuVucs]);
    }

    /**
     * Lưu Bàn Ăn mới.
     */
    public function store(Request $request)
    {
        $rules = [
            'khu_vuc_id' => 'required|exists:khu_vuc,id',
            'so_ban' => 'required|string|max:50|unique:ban_an,so_ban',
            'so_ghe' => 'required|integer|min:1',
            'trang_thai' => 'required|in:trong,khong_su_dung', // Chỉ cho phép tạo bàn Trống hoặc Hỏng
        ];
        
        $request->validate($rules);

        try {
            $uniqueCode = Str::random(12);
            $baseUrl = config('app.url') . '/order';

            BanAn::create([
                'khu_vuc_id' => $request->khu_vuc_id,
                'so_ban' => $request->so_ban,
                'so_ghe' => $request->so_ghe,
                'trang_thai' => trim($request->trang_thai),
                'ma_qr' => $uniqueCode,
                'duong_dan_qr' => $baseUrl . '?table_code=' . $uniqueCode,
            ]);

            return redirect()->route('admin.khu-vuc-ban-an')->with('success', 'Tạo bàn ăn mới thành công!');
        } catch (QueryException $e) {
            return back()->with('error', 'Lỗi DB: Bàn ăn có thể đã tồn tại.');
        }
    }

    /**
     * Hiển thị Form Sửa Bàn Ăn.
     */
    public function edit($id)
    {
        try {
            $banAn = BanAn::findOrFail($id);
            $khuVucs = KhuVuc::orderBy('tang')->get();
            return view('admins.ban-an.edit', compact('banAn', 'khuVucs'));
        } catch (\Exception $e) {
            return redirect()->route('admin.khu-vuc-ban-an')->with('error', 'Không tìm thấy Bàn ăn.');
        }
    }

    /**
     * Cập nhật Bàn Ăn.
     */
    /**
     * Cập nhật Bàn Ăn (Có logic chặn sửa trạng thái khi đang bận)
     */
    public function update(Request $request, $id)
    {
        $banAn = BanAn::findOrFail($id);

        $request->validate([
            'khu_vuc_id' => 'required|exists:khu_vuc,id',
            'so_ban' => ['required', 'string', 'max:50', Rule::unique('ban_an', 'so_ban')->ignore($banAn->id)],
            'so_ghe' => 'required|integer|min:1',
            // Vẫn cho phép validate đủ 4 trạng thái để nhận giá trị cũ gửi lên
            'trang_thai' => 'required|in:trong,dang_phuc_vu,da_dat,khong_su_dung',
        ]);

        try {
            $trangThaiMoi = trim($request->trang_thai);
            $trangThaiCu = $banAn->trang_thai;
            if (in_array($trangThaiCu, ['dang_phuc_vu', 'da_dat'])) {
                if ($trangThaiMoi !== $trangThaiCu) {
                    return back()->with('error', '❌ Bàn đang có khách hoặc đã được đặt. Bạn không thể thay đổi trạng thái lúc này. Vui lòng hoàn tất đơn hàng trước.');
                }
            }

            $banAn->update([
                'khu_vuc_id' => $request->khu_vuc_id,
                'so_ban' => $request->so_ban,
                'so_ghe' => $request->so_ghe,
                'trang_thai' => $trangThaiMoi,
            ]);

            return redirect()->route('admin.khu-vuc-ban-an')->with('success', "Cập nhật thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống.');
        }
    }

    /**
     * Xóa Bàn Ăn.
     */
    public function destroy($id)
    {
        try {
            $banAn = BanAn::findOrFail($id);
            $trangThai = trim(strtolower($banAn->trang_thai));

            // 1. Chặn xóa nếu bàn đang phục vụ hoặc đã đặt (theo DB)
            if (in_array($trangThai, ['dang_phuc_vu', 'da_dat'])) {
                return back()->with('error', "❌ Bàn đang có khách hoặc đã được giữ chỗ, không thể xóa.");
            }
            
            // 2. Chặn xóa nếu có đơn đặt sắp tới (Double check trong bảng DatBan)
            $hasBooking = DatBan::where('ban_id', $id)
                ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
                ->exists();

            if ($hasBooking) {
                return back()->with('error', "❌ Bàn đang có đơn đặt, không thể xóa.");
            }

            $banAn->delete();
            return redirect()->route('admin.khu-vuc-ban-an')->with('success', "✅ Xóa bàn thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống.');
        }
    }

    /**
     * Tái tạo Mã QR.
     */
    public function regenerateQr($id)
    {
        try {
            $banAn = BanAn::findOrFail($id);
            $newUniqueCode = Str::random(12);
            $baseUrl = config('app.url') . '/order';

            $banAn->update([
                'ma_qr' => $newUniqueCode,
                'duong_dan_qr' => $baseUrl . '?table_code=' . $newUniqueCode,
            ]);
            return back()->with('success', "🔄 Tái tạo QR thành công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi hệ thống.');
        }
    }

    /**
     * AJAX: Tìm bàn trống theo giờ (Đã fix logic chặn chặt chẽ)
     */
    public function ajaxGetAvailableTables(Request $request)
    {
        $selectedTime = $request->input('time');
        if (!$selectedTime) return response()->json(['error' => 'Vui lòng chọn giờ.'], 400);

        // 1. ÉP MÚI GIỜ
        $timezone = 'Asia/Ho_Chi_Minh';
        $now = Carbon::now($timezone);
        $newStart = Carbon::parse($selectedTime, $timezone);
        $duration = 120; 

        $excludedIds = [];
        
        // 2. CHẶN BÀN ĐANG PHỤC VỤ (Nếu đặt quá gần giờ hiện tại)
        // Nếu khách đặt trong vòng [Hiện tại -> 2.5 tiếng tới], loại bỏ bàn đang ăn
        $thoiDiemGiaiPhong = $now->copy()->addMinutes($duration + 30);
        
        if ($newStart->lt($thoiDiemGiaiPhong)) {
            $busyIds = BanAn::whereIn('trang_thai', ['dang_phuc_vu', 'da_dat'])->pluck('id')->toArray();
            $excludedIds = array_merge($excludedIds, $busyIds);
        }

        // 3. CHẶN BÀN TRÙNG LỊCH (Future Booking)
        $conflictingIds = DatBan::whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->whereBetween('gio_den', [
                $newStart->copy()->subMinutes($duration - 1),
                $newStart->copy()->addMinutes($duration - 1)
            ])->pluck('ban_id')->toArray();

        $excludedIds = array_unique(array_merge($excludedIds, $conflictingIds));

        // 4. LẤY KẾT QUẢ
        $availableTables = BanAn::where('trang_thai', '!=', 'khong_su_dung')
            ->whereNotIn('id', $excludedIds)
            ->with('khuVuc')
            ->orderBy('so_ban')->get();

        $result = $availableTables->map(function($ban) {
            return [
                'id' => $ban->id,
                'so_ban' => $ban->so_ban,
                'so_ghe' => $ban->so_ghe,
                'khu_vuc' => $ban->khuVuc ? $ban->khuVuc->ten_khu_vuc : '',
                'trang_thai' => $ban->trang_thai
            ];
        });

        return response()->json($result);
    }

    public function showQrGeneratorPage()
    {
        $banAns = BanAn::orderBy('id')->get();
        return view('admins.qr_generator', ['banAns' => $banAns]);
    }
}