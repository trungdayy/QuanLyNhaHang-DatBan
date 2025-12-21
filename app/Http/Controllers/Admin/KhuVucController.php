<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhuVuc;
use App\Models\BanAn;   // 💡 Thêm Model BanAn
use App\Models\DatBan;  // 💡 Thêm Model DatBan
use Carbon\Carbon;      // 💡 Thêm Carbon để xử lý giờ
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class KhuVucController extends Controller
{
    /**
     * Hiển thị trang quản lý Khu Vực & Bàn Ăn.
     */
    public function showManagementPage()
    {
        $khuVucs = collect();
        $banAns = collect(); // Khởi tạo collection rỗng
        $errorMessage = null;

        try {
            // 1. Lấy danh sách Khu vực
            $khuVucs = KhuVuc::orderBy('tang')->get();

            // 2. Lấy tất cả bàn ăn để tính toán trạng thái
            $banAns = BanAn::all();

            // 💡 LOGIC TÍNH TOÁN TRẠNG THÁI & TỰ SỬA LỖI (COPY TỪ BanAnController)
            $timezone = 'Asia/Ho_Chi_Minh';
            $now = Carbon::now($timezone);
            $limitTime = $now->copy()->addMinutes(30);

            // 3. Đồng bộ trạng thái bàn với trạng thái khu vực
            foreach ($khuVucs as $khuVuc) {
                $trangThaiKhuVuc = trim(strtolower($khuVuc->trang_thai ?? 'dang_su_dung'));
                
                if ($trangThaiKhuVuc === 'khong_su_dung') {
                    // Nếu khu vực bị tắt, đảm bảo tất cả bàn trong khu vực đó cũng bị tắt
                    // (trừ những bàn đang phục vụ hoặc đã đặt)
                    $banTrongKhuVuc = BanAn::where('khu_vuc_id', $khuVuc->id)->get();
                    
                    foreach ($banTrongKhuVuc as $ban) {
                        $trangThaiBan = trim(strtolower($ban->trang_thai));
                        
                        // Chỉ tắt bàn nếu bàn đang trống (không phục vụ, không đặt)
                        if (!in_array($trangThaiBan, ['dang_phuc_vu', 'da_dat', 'khong_su_dung'])) {
                            $ban->update(['trang_thai' => 'khong_su_dung']);
                        }
                    }
                }
            }

            $banAns->transform(function ($ban) use ($now, $limitTime, $khuVucs) {
                
                // Kiểm tra xem khu vực của bàn có bị tắt không
                $khuVuc = $khuVucs->firstWhere('id', $ban->khu_vuc_id);
                if ($khuVuc) {
                    $trangThaiKhuVuc = trim(strtolower($khuVuc->trang_thai ?? 'dang_su_dung'));
                    if ($trangThaiKhuVuc === 'khong_su_dung') {
                        // Nếu khu vực bị tắt, giữ nguyên trạng thái bàn (không tính toán lại)
                        // Nếu bàn chưa bị tắt nhưng khu vực đã tắt, thì tắt bàn (trừ bàn đang phục vụ/đã đặt)
                        $trangThaiBan = trim(strtolower($ban->trang_thai));
                        if (!in_array($trangThaiBan, ['dang_phuc_vu', 'da_dat', 'khong_su_dung'])) {
                            $ban->trang_thai = 'khong_su_dung';
                            $ban->save();
                        }
                        return $ban;
                    }
                }
                
                // Bàn hỏng -> Bỏ qua
                if ($ban->trang_thai === 'khong_su_dung') {
                    return $ban;
                }

                $trangThaiMoi = 'trong'; // Mặc định là Trống
                $thongTinBooking = null;

                // Check 1: Đang phục vụ? (Tìm đơn 'khach_da_den')
                $dangPhucVu = DatBan::where('ban_id', $ban->id)
                    ->where('trang_thai', 'khach_da_den')
                    ->first();

                if ($dangPhucVu) {
                    $trangThaiMoi = 'dang_phuc_vu';
                    $thongTinBooking = "Đang phục vụ: {$dangPhucVu->ten_khach}";
                } else {
                    // Check 2: Đã đặt sắp tới? (Tìm đơn 'da_xac_nhan')
                    $sapToi = DatBan::where('ban_id', $ban->id)
                        ->where('trang_thai', 'da_xac_nhan')
                        ->where(function($q) use ($now, $limitTime) {
                            $q->whereBetween('gio_den', [$now, $limitTime])
                              ->orWhere(function($sub) use ($now) {
                                  $sub->where('gio_den', '<', $now)
                                      ->where('gio_den', '>', $now->copy()->subMinutes(60));
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

                // Tự động sửa DB nếu trạng thái thực tế khác DB
                if ($ban->trang_thai !== $trangThaiMoi) {
                    $ban->trang_thai = $trangThaiMoi;
                    $ban->save();
                }

                $ban->booking_info = $thongTinBooking;
                return $ban;
            });

        } catch (QueryException $e) {
            $errorMessage = "Lỗi Database: " . $e->getMessage();
            Log::error("DB QUERY CRASH (KhuVuc): " . $e->getMessage());
        } catch (\Exception $e) {
            $errorMessage = "Lỗi hệ thống không xác định: " . $e->getMessage();
            Log::error("System CRASH: " . $e->getMessage());
        }

        // Trả về view kèm biến $banAns đã xử lý
        return view('admins.khu-vuc-ban-an', [
            'khuVucs' => $khuVucs,
            'banAns' => $banAns, 
            'errorMessage' => $errorMessage
        ]);
    }

    /**
     * Hiển thị Form Tạo Khu Vuc Mới.
     */
    public function create()
    {
        return view('admins.khu-vuc.create');
    }

    /**
     * Lưu Khu Vuc mới vào database.
     */
    public function store(Request $request)
    {
        $rules = [
            'ten_khu_vuc' => 'required|string|max:100|unique:khu_vuc,ten_khu_vuc',
            'tang' => 'required|integer|min:1',
            'mo_ta' => 'required|string|max:255',
        ];

        $messages = [
            'ten_khu_vuc.required' => 'Vui lòng nhập Tên Khu vực.',
            'ten_khu_vuc.unique' => 'Tên Khu vực này đã tồn tại.',
            'ten_khu_vuc.max' => 'Tên Khu vực không được vượt quá 100 ký tự.',
            'tang.required' => 'Vui lòng nhập Số tầng.',
            'tang.integer' => 'Số tầng phải là số nguyên.',
            'tang.min' => 'Số tầng phải lớn hơn 0.',
            'mo_ta.required' => 'Vui lòng nhập Mô tả.',
            'mo_ta.max' => 'Mô tả không được vượt quá 255 ký tự.',
        ];

        $request->validate($rules, $messages);

        try {
            KhuVuc::create($request->all());

            return redirect()->route('admin.khu-vuc-ban-an')
                ->with('success', 'Tạo khu vực mới thành công!');
        } catch (QueryException $e) {
            Log::error("DB CREATE FAILED (KhuVuc): " . $e->getMessage());
            return back()->with('error', 'Lỗi DB: Khu vực có thể đã tồn tại hoặc dữ liệu không hợp lệ.');
        }
    }

    /**
     * Hiển thị Form Sửa Khu Vuc.
     */
    public function edit($id)
    {
        try {
            $khuVuc = KhuVuc::findOrFail($id);
            return view('admins.khu-vuc.edit', ['khuVuc' => $khuVuc]);
        } catch (\Exception $e) {
            Log::error("EDIT KHUVUC FAILED: " . $e->getMessage());
            return redirect()->route('admin.khu-vuc-ban-an')
                ->with('error', 'Không tìm thấy Khu vực để sửa.');
        }
    }

    /**
     * Cập nhật Khu Vuc trong database.
     */
    public function update(Request $request, $id)
    {
        $khuVuc = KhuVuc::findOrFail($id);

        $rules = [
            'ten_khu_vuc' => ['required', 'string', 'max:100', Rule::unique('khu_vuc', 'ten_khu_vuc')->ignore($khuVuc->id)],
            'tang' => 'required|integer|min:1',
            'mo_ta' => 'required|string|max:255',
        ];

        $messages = [
            'ten_khu_vuc.required' => 'Vui lòng nhập Tên Khu vực.',
            'ten_khu_vuc.unique' => 'Tên Khu vực này đã tồn tại.',
            'ten_khu_vuc.max' => 'Tên Khu vực không được vượt quá 100 ký tự.',
            'tang.required' => 'Vui lòng nhập Số tầng.',
            'tang.integer' => 'Số tầng phải là số nguyên.',
            'tang.min' => 'Số tầng phải lớn hơn 0.',
            'mo_ta.required' => 'Vui lòng nhập Mô tả.',
            'mo_ta.max' => 'Mô tả không được vượt quá 255 ký tự.',
        ];

        $request->validate($rules, $messages);

        try {
            $khuVuc->update($request->all());
            return redirect()->route('admin.khu-vuc-ban-an')
                ->with('success', "Cập nhật Khu vực {$khuVuc->ten_khu_vuc} thành công!");
        } catch (\Exception $e) {
            Log::error("DB UPDATE FAILED (KhuVuc): " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống khi cập nhật khu vực.');
        }
    }

    /**
     * Xóa Khu Vuc khỏi database.
     */
    public function destroy($id)
    {
        try {
            $khuVuc = KhuVuc::findOrFail($id);

            if ($khuVuc->banAns()->exists()) {
                return back()->with('error', "Không thể xóa Khu vực {$khuVuc->ten_khu_vuc} vì vẫn còn bàn ăn liên kết.");
            }

            $khuVuc->delete();

            return redirect()->route('admin.khu-vuc-ban-an')
                ->with('success', "Xóa Khu vực {$khuVuc->ten_khu_vuc} thành công!");
        } catch (\Exception $e) {
            Log::error("DELETE KHUVUC FAILED: " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống khi xóa khu vực.');
        }
    }

    /**
     * Toggle trạng thái khu vực (Tắt/Mở)
     * Tắt: đổi trạng thái thành khong_su_dung và tắt tất cả bàn trong khu vực (trừ bàn đang phục vụ/đã đặt)
     * Bật: đổi trạng thái thành dang_su_dung và bật lại các bàn trong khu vực (set về trong nếu không có đơn đặt)
     */
    public function toggleStatus($id)
    {
        try {
            $khuVuc = KhuVuc::findOrFail($id);
            $trangThaiHienTai = trim(strtolower($khuVuc->trang_thai ?? 'dang_su_dung'));

            // Toggle trạng thái khu vực
            if ($trangThaiHienTai === 'khong_su_dung') {
                // Bật khu vực: chuyển từ khong_su_dung sang dang_su_dung
                $khuVuc->update(['trang_thai' => 'dang_su_dung']);
                
                // Bật lại các bàn trong khu vực (chỉ những bàn đang tắt và không có đơn đặt)
                $timezone = 'Asia/Ho_Chi_Minh';
                $now = Carbon::now($timezone);
                $limitTime = $now->copy()->addMinutes(30);
                
                $banAns = BanAn::where('khu_vuc_id', $khuVuc->id)
                    ->where('trang_thai', 'khong_su_dung')
                    ->get();
                
                foreach ($banAns as $ban) {
                    // Kiểm tra xem bàn có đang phục vụ hoặc đã đặt không
                    $dangPhucVu = DatBan::where('ban_id', $ban->id)
                        ->where('trang_thai', 'khach_da_den')
                        ->exists();
                    
                    $daDat = DatBan::where('ban_id', $ban->id)
                        ->where('trang_thai', 'da_xac_nhan')
                        ->where(function($q) use ($now, $limitTime) {
                            $q->whereBetween('gio_den', [$now, $limitTime])
                              ->orWhere(function($sub) use ($now) {
                                  $sub->where('gio_den', '<', $now)
                                      ->where('gio_den', '>', $now->copy()->subMinutes(60));
                              });
                        })
                        ->exists();
                    
                    // Chỉ bật bàn nếu không có đơn đặt
                    if (!$dangPhucVu && !$daDat) {
                        $ban->update(['trang_thai' => 'trong']);
                    }
                }
                
                return back()->with('success', "✅ Đã bật khu vực {$khuVuc->ten_khu_vuc}.");
            } else {
                // Tắt khu vực: chuyển từ dang_su_dung sang khong_su_dung
                // KIỂM TRA: Nếu có bàn đang phục vụ hoặc đã đặt, không cho phép tắt
                $timezone = 'Asia/Ho_Chi_Minh';
                $now = Carbon::now($timezone);
                $limitTime = $now->copy()->addMinutes(30);
                
                $banAns = BanAn::where('khu_vuc_id', $khuVuc->id)->get();
                $banDangPhucVu = [];
                $banDaDat = [];
                
                foreach ($banAns as $ban) {
                    $trangThaiBan = trim(strtolower($ban->trang_thai));
                    
                    // Kiểm tra bàn đang phục vụ
                    if ($trangThaiBan === 'dang_phuc_vu') {
                        $datBan = DatBan::where('ban_id', $ban->id)
                            ->where('trang_thai', 'khach_da_den')
                            ->first();
                        if ($datBan) {
                            $banDangPhucVu[] = $ban->so_ban;
                        }
                    }
                    
                    // Kiểm tra bàn đã đặt
                    if ($trangThaiBan === 'da_dat') {
                        $datBan = DatBan::where('ban_id', $ban->id)
                            ->where('trang_thai', 'da_xac_nhan')
                            ->where(function($q) use ($now, $limitTime) {
                                $q->whereBetween('gio_den', [$now, $limitTime])
                                  ->orWhere(function($sub) use ($now) {
                                      $sub->where('gio_den', '<', $now)
                                          ->where('gio_den', '>', $now->copy()->subMinutes(60));
                                  });
                            })
                            ->first();
                        if ($datBan) {
                            $banDaDat[] = $ban->so_ban;
                        }
                    }
                }
                
                // Nếu có bàn đang phục vụ hoặc đã đặt, không cho phép tắt
                if (!empty($banDangPhucVu) || !empty($banDaDat)) {
                    $message = "❌ Không thể tắt khu vực {$khuVuc->ten_khu_vuc} vì:";
                    if (!empty($banDangPhucVu)) {
                        $message .= " Có bàn đang phục vụ: " . implode(', ', $banDangPhucVu) . ".";
                    }
                    if (!empty($banDaDat)) {
                        $message .= " Có bàn đã được đặt: " . implode(', ', $banDaDat) . ".";
                    }
                    $message .= " Vui lòng chờ khi tất cả bàn trống.";
                    return back()->with('error', $message);
                }
                
                // Tất cả bàn đã trống, cho phép tắt khu vực
                $khuVuc->update(['trang_thai' => 'khong_su_dung']);
                
                // Tắt tất cả bàn trong khu vực
                foreach ($banAns as $ban) {
                    $ban->update(['trang_thai' => 'khong_su_dung']);
                }
                
                return back()->with('success', "🔒 Đã tắt khu vực {$khuVuc->ten_khu_vuc}.");
            }
        } catch (\Exception $e) {
            Log::error("Lỗi toggle trạng thái khu vực: " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống.');
        }
    }
}