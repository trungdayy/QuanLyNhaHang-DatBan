<?php

namespace App\Services;

use App\Models\BanAn;
use App\Models\DatBan;
use App\Models\ChiTietHoaDon;
use Carbon\Carbon;

class BookingAvailabilityService
{
    const PREPARE_TIME = 15;
    const DINING_TIME  = 120;
    const CLEAN_TIME   = 15;

    public function checkAvailability($soNguoi, $gioDen)
    {
        // =================================================================
        // 1. TÍNH TOÁN KHUNG GIỜ
        // =================================================================
        $requestedTime = Carbon::parse($gioDen);
        $blockStart = $requestedTime->copy()->subMinutes(self::PREPARE_TIME); 
        $blockEnd   = $requestedTime->copy()->addMinutes(self::DINING_TIME + self::CLEAN_TIME);

        // Biến lưu thời gian giải phóng để gợi ý
        $releaseTimes = [];

        // =================================================================
        // 2. LOGIC RIÊNG: NẾU KHÁCH <= 4 NGƯỜI (XỬ LÝ TRÀN ZONE A -> B)
        // =================================================================
        if ($soNguoi <= 4) {
            // --- A. Lấy Tổng Số Bàn (Capacity) ---
            // Khu A (1-2 người) - Loại bỏ bàn dự phòng 5,9
            $capA = BanAn::where('khu_vuc_id', 1)->whereNotIn('id', [5, 9])->where('trang_thai', '!=', 'khong_su_dung')->count();
            // Khu B (3-4 người)
            $capB = BanAn::where('khu_vuc_id', 2)->whereNotIn('id', [5, 9])->where('trang_thai', '!=', 'khong_su_dung')->count();

            // --- B. Tính Nhu Cầu Thực Tế (Demand) ---
            // Đếm khách Đặt Online + Khách Đang Ngồi (Walk-in)
            // Nhóm A: 1-2 người
            $demandA = $this->countActiveGuests(1, 2, 1, $blockStart, $blockEnd, $releaseTimes); 
            // Nhóm B: 3-4 người
            $demandB = $this->countActiveGuests(3, 4, 2, $blockStart, $blockEnd, $releaseTimes); 

            // --- C. Logic Tràn (Overflow) ---
            // Nếu khách A đông hơn bàn A, số dư sẽ tràn sang chiếm chỗ B
            // Ví dụ: Có 2 bàn A, nhưng có 3 khách đặt -> Overflow = 1
            $overflowA = max(0, $demandA - $capA);
            
            // Tổng tải trọng thực tế lên Khu B = Khách B thực tế + Khách A tràn sang
            $totalLoadOnB = $demandB + $overflowA;

            $isAvailable = false;

            if ($soNguoi <= 2) {
                // TRƯỜNG HỢP KHÁCH 2 NGƯỜI:
                // 1. Còn bàn ở đúng Khu A không? (Nhu cầu < Sức chứa)
                if ($demandA < $capA) {
                    $isAvailable = true; 
                } 
                // 2. Nếu A hết, kiểm tra xem B còn gánh được không?
                elseif ($totalLoadOnB < $capB) {
                    $isAvailable = true; 
                }
            } else {
                // TRƯỜNG HỢP KHÁCH 4 NGƯỜI:
                // Bắt buộc ngồi B. Kiểm tra xem B còn chỗ không (sau khi đã bị A chiếm bớt)
                if ($totalLoadOnB < $capB) {
                    $isAvailable = true;
                }
            }

            if ($isAvailable) {
                return ['status' => true];
            } else {
                return $this->suggestionResponse($soNguoi, $releaseTimes);
            }
        }

        // =================================================================
        // 3. LOGIC CŨ CHO KHÁCH ĐÔNG (>4 NGƯỜI) - GIỮ NGUYÊN
        // =================================================================
        $khuVucId = null;
        if ($soNguoi <= 8) $khuVucId = 3;      // Khu C
        elseif ($soNguoi <= 12) $khuVucId = 4; // Khu D
        else return ['status' => false, 'message' => 'Số lượng khách quá lớn.'];

        $capacity = BanAn::where('khu_vuc_id', $khuVucId)->whereNotIn('id', [5, 9])->where('trang_thai', '!=', 'khong_su_dung')->count();
        
        $minP = ($khuVucId == 3) ? 5 : 9;
        $maxP = ($khuVucId == 3) ? 8 : 12;

        $demand = $this->countActiveGuests($minP, $maxP, $khuVucId, $blockStart, $blockEnd, $releaseTimes);

        if ($demand < $capacity) {
            return ['status' => true];
        }

        return $this->suggestionResponse($soNguoi, $releaseTimes);
    }

    /**
     * Hàm phụ: Đếm số lượng khách đang chiếm chỗ (Cả Online lẫn Walk-in)
     * Đồng thời thu thập giờ trả bàn để gợi ý.
     */
    private function countActiveGuests($minPax, $maxPax, $khuVucId, $start, $end, &$releaseTimes)
    {
        $count = 0;

        // 1. Đếm Đặt Bàn Online
        $bookings = DatBan::whereBetween('nguoi_lon', [$minPax, $maxPax])
            ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
            ->where(function ($q) use ($start, $end) {
                $q->whereRaw("DATE_SUB(gio_den, INTERVAL ? MINUTE) < ?", [self::PREPARE_TIME, $end])
                  ->whereRaw("DATE_ADD(gio_den, INTERVAL ? MINUTE) > ?", [self::DINING_TIME + self::CLEAN_TIME, $start]);
            })
            ->get();

        foreach ($bookings as $b) {
            $count++;
            $releaseTimes[] = Carbon::parse($b->gio_den)->addMinutes(self::DINING_TIME + self::CLEAN_TIME);
        }

        // 2. Đếm Khách Vãng Lai (Walk-in) đang ngồi tại Khu Vực đó
        // Logic: Lấy các bàn đang 'dang_phuc_vu' thuộc khu vực $khuVucId
        $servingTables = BanAn::where('khu_vuc_id', $khuVucId)
            ->whereNotIn('id', [5, 9])
            ->where('trang_thai', 'dang_phuc_vu')
            ->get();

        $limitCheckTime = Carbon::now()->addMinutes(self::DINING_TIME + self::CLEAN_TIME);

        // Chỉ tính khách đang ngồi nếu thời gian họ ngồi đè lên thời gian khách mới muốn đặt
        if ($start < $limitCheckTime) {
            foreach ($servingTables as $table) {
                // Lấy giờ vào
                $lastSession = ChiTietHoaDon::where('ban_so', $table->so_ban)->latest('created_at')->first();
                $entryTime = $lastSession ? ($lastSession->gio_vao ? Carbon::parse($lastSession->gio_vao) : $lastSession->created_at) : Carbon::now();
                $freeTime = $entryTime->copy()->addMinutes(self::DINING_TIME + self::CLEAN_TIME);

                if ($start < $freeTime) {
                    $count++;
                    $releaseTimes[] = $freeTime;
                }
            }
        }

        return $count;
    }

    /**
     * Hàm phụ: Trả về thông báo gợi ý
     */
    private function suggestionResponse($soNguoi, $releaseTimes)
    {
        $suggestion = " Vui lòng chọn giờ khác.";
        
        if (count($releaseTimes) > 0) {
            sort($releaseTimes);
            // Cộng thêm thời gian chuẩn bị để khách đặt vào là được ngay
            $nearestSlot = $releaseTimes[0]->addMinutes(self::PREPARE_TIME);
            
            $timeStr = ($nearestSlot < Carbon::now()) ? "ngay bây giờ" : $nearestSlot->format('H:i');
            $suggestion = " Bàn sớm nhất sẽ trống vào lúc **$timeStr**. Bạn có thể đặt từ giờ đó trở đi.";
        }

        return [
            'status' => false, 
            'message' => "Rất tiếc, khung giờ này bàn cho $soNguoi người đã kín chỗ (bao gồm cả bàn ghép).$suggestion"
        ];
    }
}