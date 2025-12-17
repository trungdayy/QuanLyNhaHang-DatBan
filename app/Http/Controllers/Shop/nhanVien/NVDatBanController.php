<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\NhanVien;
use App\Models\ChiTietDatBan;
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
        $query = DatBan::with([
            'banAn',
            'nhanVien',
            'chiTietDatBan.combo',
            'orderMon'
        ]);

        if ($r->trang_thai) $query->where('trang_thai', $r->trang_thai);

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

        if ($r->ma) $query->where('ma_dat_ban', 'like', '%' . $r->ma . '%');

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
            ->whereIn('vai_tro', ['le_tan', 'phuc_vu', 'quan_ly'])
            ->get();
        // Không cần load sẵn bàn trống ở đây nữa vì sẽ dùng AJAX để load bàn phù hợp
        return view('shop.nhanvien.datban.create', compact('combos', 'nhanViens'));
    }

/**
     * AJAX: Kiểm tra bàn trống THÔNG MINH (Offline - Ưu tiên Kho Dự Phòng)
     */
public function ajaxCheckBanTrong(Request $request)
    {
        $checkTime = $request->input('time') ? Carbon::parse($request->input('time')) : Carbon::now();
        $soKhach = (int) $request->input('so_khach', 1);

        // 1. Lấy tất cả bàn khả dụng
        $tables = \App\Models\BanAn::where('trang_thai', '!=', 'khong_su_dung')
            ->where('so_ghe', '>=', $soKhach)
            ->orderBy('so_ghe', 'asc')
            ->orderBy('so_ban', 'asc')
            ->get();

        // 2. Lấy danh sách Đơn Online "Chưa xếp bàn" (Phantom Bookings)
        $unassignedBookings = \App\Models\DatBan::whereNull('ban_id')
            ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
            ->where(function ($q) use ($checkTime) {
                // Logic trùng giờ
                $q->where('gio_den', '<=', $checkTime)
                  ->whereRaw("DATE_ADD(gio_den, INTERVAL IFNULL(thoi_luong_phut, 120) MINUTE) > ?", [$checkTime]);
                // Hoặc đơn sắp đến
                $q->orWhereBetween('gio_den', [$checkTime, $checkTime->copy()->addMinutes(120)]);
            })
            ->orderBy('nguoi_lon', 'desc')
            ->get();

        $result = [];
        // [MỚI] Biến cờ để kiểm tra xem có bàn nào cho khách ngồi được ngay không
        $hasFreeTables = false;
        $hasLimitedTables = false;

        foreach ($tables as $table) {
            $prefix = "";
            if ($table->khu_vuc_id == 9) $prefix = "[SOS] ";

            // --- BƯỚC 1: KIỂM TRA BÀN CÓ ĐANG BỊ GÁN CỨNG (KHÁCH NGỒI) KHÔNG? ---
            $physicallyOccupied = \App\Models\DatBan::where('ban_id', $table->id)
                ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
                ->where('gio_den', '<=', $checkTime)
                ->whereRaw("DATE_ADD(gio_den, INTERVAL IFNULL(thoi_luong_phut, 120) MINUTE) > ?", [$checkTime])
                ->exists();

            if ($physicallyOccupied) {
                continue; // Bàn đang có khách ngồi -> Bỏ qua luôn
            }

            // --- BƯỚC 2: KIỂM TRA GAP FILLING (KHÁCH TƯƠNG LAI) ---
            $nextHardBooking = \App\Models\DatBan::where('ban_id', $table->id)
                ->where('gio_den', '>', $checkTime)
                ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
                ->orderBy('gio_den', 'asc')
                ->first();

            $minutesAvailable = 9999;
            if ($nextHardBooking) {
                $minutesAvailable = $checkTime->diffInMinutes(Carbon::parse($nextHardBooking->gio_den), false) - 15;
            }

            // --- BƯỚC 3: MÔ PHỎNG GÁN ĐƠN ONLINE (GÁN ẢO) ---
            $isReservedForOnline = false;
            $onlineBookingInfo = null;

            if (!in_array($table->khu_vuc_id, [5, 9])) { 
                foreach ($unassignedBookings as $key => $booking) {
                    if ($table->so_ghe >= $booking->nguoi_lon) {
                        $isReservedForOnline = true;
                        $onlineBookingInfo = $booking;
                        unset($unassignedBookings[$key]); // Đơn này đã có chỗ (ảo) nên xóa khỏi list chờ
                        break; 
                    }
                }
            }

            // --- BƯỚC 4: TỔNG HỢP KẾT QUẢ ---
            if ($isReservedForOnline) {
                // Đây là bàn phải giữ cho Online
                $gioKhach = Carbon::parse($onlineBookingInfo->gio_den)->format('H:i');
                $result[] = [
                    'id' => $table->id,
                    'so_ban' => $prefix . $table->so_ban,
                    'so_ghe' => $table->so_ghe,
                    'trang_thai' => 'reserved', // Đặt trạng thái riêng để tí nữa lọc
                    'khu_vuc_id' => $table->khu_vuc_id,
                    'phut_con_lai' => 0,
                    'message' => "⚠️ Giữ cho đơn Online {$onlineBookingInfo->ma_dat_ban} lúc {$gioKhach}"
                ];
            } 
            elseif ($minutesAvailable < 9999) {
                // Bàn có thể ngồi được nhưng bị giới hạn thời gian
                if ($minutesAvailable >= 45) {
                    $hasLimitedTables = true; // [QUAN TRỌNG] Đánh dấu là có bàn Limited
                    $gioKhachSau = Carbon::parse($nextHardBooking->gio_den)->format('H:i');
                    $result[] = [
                        'id' => $table->id,
                        'so_ban' => $prefix . $table->so_ban,
                        'so_ghe' => $table->so_ghe,
                        'trang_thai' => 'limited',
                        'phut_con_lai' => $minutesAvailable,
                        'khu_vuc_id' => $table->khu_vuc_id,
                        'message' => "Trống {$minutesAvailable}p (Khách sau: {$gioKhachSau})"
                    ];
                }
            } 
            else {
                // Bàn trống hoàn toàn
                $hasFreeTables = true; // [QUAN TRỌNG] Đánh dấu là có bàn Free
                $result[] = [
                    'id' => $table->id,
                    'so_ban' => $prefix . $table->so_ban,
                    'so_ghe' => $table->so_ghe,
                    'trang_thai' => 'free',
                    'phut_con_lai' => 9999,
                    'khu_vuc_id' => $table->khu_vuc_id,
                    'message' => "Trống"
                ];
            }
        }

        // [LOGIC MỚI] LỌC KẾT QUẢ
        // Nếu quán vẫn còn bàn Free hoặc Limited -> Ẩn hết mấy bàn 'reserved' đi cho đỡ rối
        // Nếu quán FULL sạch (không còn Free/Limited) -> Mới hiện 'reserved' ra
        if ($hasFreeTables || $hasLimitedTables) {
            $result = array_filter($result, function($item) {
                return $item['trang_thai'] !== 'reserved';
            });
        }

        // --- SẮP XẾP ---
        usort($result, function ($a, $b) {
            // 1. Ưu tiên KHO/SOS
            $isA_Backup = in_array($a['khu_vuc_id'], [5, 9]);
            $isB_Backup = in_array($b['khu_vuc_id'], [5, 9]);
            if ($isA_Backup && !$isB_Backup) return -1;
            if (!$isA_Backup && $isB_Backup) return 1;

            // 2. Ưu tiên bàn Free
            if ($a['trang_thai'] === 'free' && $b['trang_thai'] !== 'free') return -1;
            if ($a['trang_thai'] !== 'free' && $b['trang_thai'] === 'free') return 1;

            // 3. Ưu tiên bàn Limited
            if ($a['trang_thai'] === 'limited' && $b['trang_thai'] !== 'limited') return -1;
            if ($a['trang_thai'] !== 'limited' && $b['trang_thai'] === 'limited') return 1;

            return $a['so_ghe'] <=> $b['so_ghe'];
        });

        // Re-index lại mảng sau khi filter (để JSON trả về dạng mảng [] chứ không phải object {})
        return response()->json(array_values($result));
    }

    /**
     * Xử lý lưu đơn đặt bàn mới.
     */
    public function store(Request $request)
    {
        $now = Carbon::now();

        // 1. Validate cơ bản
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'tong_khach' => 'required|integer|min:1',
            'ban_id' => [
                'required',
                'exists:ban_an,id',
                // Chỉ chặn bàn bị khóa (khong_su_dung)
                // Logic kiểm tra xung đột lịch và trạng thái bàn sẽ được xử lý ở bước sau
                Rule::exists('ban_an', 'id')->where('trang_thai', '!=', 'khong_su_dung'),
            ],
            'gio_den' => 'required|date',
        ], [
            'ban_id.required' => 'Vui lòng chọn bàn.',
            'ban_id.exists' => 'Bàn được chọn không tồn tại hoặc đang bị khóa.',
        ]);

        $tongKhach = $request->nguoi_lon + $request->tre_em;
        
        // Logic kiểm tra số lượng Combo
        $tongCombo = collect(json_decode($request->cart_data, true))->where('key', 'like', 'combo_%')->sum('quantity');
        
        if ($tongCombo > 0 && $tongCombo < $tongKhach) {
            return back()->withInput()->with('error', "Số suất Combo phải >= số khách.");
        }

        $banAn = BanAn::find($request->ban_id);
        
        // Kiểm tra bàn có tồn tại không
        if (!$banAn) {
            return back()->withInput()->with('error', "Bàn được chọn không tồn tại.");
        }
        
        // Kiểm tra bàn có bị khóa không
        if ($banAn->trang_thai === 'khong_su_dung') {
            return back()->withInput()->with('error', "Bàn này đang bị khóa, không thể sử dụng.");
        }
        
        // Kiểm tra số ghế
        if ($banAn->so_ghe < $tongKhach) {
            return back()->withInput()->with('error', "Bàn này không đủ ghế. Bàn có {$banAn->so_ghe} ghế nhưng cần {$tongKhach} ghế.");
        }

        // Tính thời lượng mong muốn (Mặc định 120p)
        $thoiLuongMongMuon = 120;
        // (Có thể thêm logic tính thời lượng dựa trên combo nếu cần)

        // [LOGIC MỚI] Kiểm tra xung đột & Tự động cắt giờ (Gap Filling)
        $start = Carbon::parse($request->gio_den);
        $endMongMuon = $start->copy()->addMinutes($thoiLuongMongMuon);

        // Tìm đơn tiếp theo SỚM NHẤT mà bị trùng với khung giờ mong muốn
        $conflict = DatBan::where('ban_id', $request->ban_id)
            ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->where('gio_den', '>', $start) // Chỉ quan tâm đơn sau giờ đến
            ->where('gio_den', '<', $endMongMuon) // Và bắt đầu trước khi mình ăn xong
            ->orderBy('gio_den', 'asc')
            ->first();

        $thoiLuongChot = $thoiLuongMongMuon;
        $messageWarning = null;

        if ($conflict) {
            // Tính khoảng trống thực tế đến khi đơn kia vào
            $conflictStart = Carbon::parse($conflict->gio_den);
            $minutesGap = $start->diffInMinutes($conflictStart, false);
            
            // Trừ hao 15p dọn dẹp
            $thoiLuongKhaDung = $minutesGap - 15;

            if ($thoiLuongKhaDung >= 60) {
                // CASE: Đủ thời gian tối thiểu (60p) -> Cho phép chèn nhưng cắt giờ
                $thoiLuongChot = $thoiLuongKhaDung;
                $messageWarning = "Lưu ý: Bàn này có khách lúc " . $conflictStart->format('H:i') . 
                                  ". Thời gian phục vụ giới hạn còn {$thoiLuongChot} phút.";
            } else {
                // CASE: Không đủ thời gian -> Báo lỗi
                return back()->withInput()->with('error', 
                    "Bàn bị kẹt lịch lúc {$conflictStart->format('H:i')}. Chỉ còn trống {$minutesGap} phút, không đủ phục vụ.");
            }
        } else {
            // Kiểm tra trường hợp đặc biệt: Mình đến trước khi đơn trước kết thúc (Collision ở đầu)
            // Logic này ít xảy ra với khách Walk-in vì bàn phải 'trong' mới chọn được
            // Nhưng cứ check cho chắc ăn
            $prevBooking = DatBan::where('ban_id', $request->ban_id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->where('gio_den', '<=', $start)
                ->whereRaw("DATE_ADD(gio_den, INTERVAL thoi_luong_phut MINUTE) > ?", [$start])
                ->first();
                
            if ($prevBooking) {
                 return back()->withInput()->with('error', "Bàn chưa giải phóng xong (Khách cũ chưa hết giờ).");
            }
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
                'gio_den' => $start,
                'thoi_luong_phut' => $thoiLuongChot, // Lưu thời gian thực tế (có thể bị cắt ngắn)
                'ghi_chu' => $request->ghi_chu . ($messageWarning ? " | [System] $messageWarning" : ""),
                'trang_thai' => 'khach_da_den',
                'nhan_vien_id' => $request->nhan_vien_id ?? Auth::id(),
                'tien_coc' => 0,
                'la_dat_online' => 0,
            ]);

            // Helper lưu combo (dùng lại hàm syncBookingDetails giống controller Online nếu có, hoặc viết trực tiếp)
            $this->syncBookingDetails($datBan, $request->cart_data);

            // Cập nhật trạng thái bàn
            if ($banAn->trang_thai === 'trong') {
                $banAn->update(['trang_thai' => 'dang_phuc_vu']);
            }

            DB::commit();

            // Nếu có warning (bị cắt giờ), dùng flash message đặc biệt
            if ($messageWarning) {
                return redirect()->route('nhanVien.datban.index')
                    ->with('warning', "Tạo đơn thành công nhưng bị giới hạn giờ! " . $messageWarning);
            }

            return redirect()->route('nhanVien.datban.index')
                ->with('success', 'Tạo đặt bàn thành công! Mã: ' . $maDatBan);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi lưu Đặt bàn NV: " . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Lưu chi tiết món/combo
     */
    private function syncBookingDetails($datBan, $cartJson)
    {
        if (empty($cartJson)) return;

        $cartItems = json_decode($cartJson, true);
        if (!is_array($cartItems)) return;

        foreach ($cartItems as $item) {
            $key = $item['key'] ?? '';
            $qty = $item['quantity'] ?? 1;

            if (Str::startsWith($key, 'combo_')) {
                ChiTietDatBan::create([
                    'dat_ban_id' => $datBan->id,
                    'combo_id'   => str_replace('combo_', '', $key),
                    'so_luong'   => $qty,
                ]);
            } elseif (Str::startsWith($key, 'mon_')) {
                ChiTietDatBan::create([
                    'dat_ban_id' => $datBan->id,
                    'mon_an_id'  => str_replace('mon_', '', $key),
                    'so_luong'   => $qty,
                ]);
            }
        }
    }

    /**
     * Thay đổi trạng thái đặt bàn (Giữ nguyên).
     */
    public function thayDoiTrangThai(Request $request, DatBan $datBan)
    {
       // ... (Giữ nguyên logic của bạn ở phần trước) ...
       // Copy logic switch case cũ vào đây
       
       $trangThaiMoi = $request->input('trang_thai');
       $banAn = $datBan->banAn;

       DB::beginTransaction();
       try {
           switch ($trangThaiMoi) {
               case 'da_xac_nhan':
                   if ($datBan->trang_thai !== 'cho_xac_nhan') return back()->with('error', 'Sai trạng thái');
                   if ($banAn) $banAn->update(['trang_thai' => 'da_dat']);
                   $datBan->nhan_vien_id = Auth::id();
                   break;

               case 'khach_da_den':
                   if ($banAn) $banAn->update(['trang_thai' => 'dang_phuc_vu']);
                   break;

               case 'huy':
                   // Chỉ trả về bàn trống nếu KHÔNG CÒN đơn nào khác đang active tại bàn đó
                   $isActive = DatBan::where('ban_id', $datBan->ban_id)
                        ->where('id', '!=', $datBan->id)
                        ->whereIn('trang_thai', ['khach_da_den']) // Chỉ quan tâm khách đang ngồi
                        ->exists();
                   
                   if (!$isActive && $banAn) {
                       $banAn->update(['trang_thai' => 'trong']);
                   }
                   break;

               case 'hoan_tat':
                   if ($datBan->trang_thai !== 'khach_da_den') return back()->with('error', 'Khách chưa đến.');
                   
                   $isStillActive = DatBan::where('ban_id', $datBan->ban_id)
                        ->where('id', '!=', $datBan->id)
                        ->where('trang_thai', 'khach_da_den')
                        ->exists();

                   if (!$isStillActive && $banAn) {
                       $banAn->update(['trang_thai' => 'trong']);
                   }

                   $datBan->update(['trang_thai' => 'hoan_tat']);
                   DB::commit();
                   return redirect()->route('nhanVien.hoadon.create', ['dat_ban_id' => $datBan->id]);
           }

           if ($trangThaiMoi !== 'hoan_tat') {
               $datBan->update(['trang_thai' => $trangThaiMoi]);
           }
           
           DB::commit();
           return back()->with('success', 'Cập nhật thành công');
       } catch (\Exception $e) {
           DB::rollBack();
           return back()->with('error', $e->getMessage());
       }
    }
}