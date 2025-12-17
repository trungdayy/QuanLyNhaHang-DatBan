<?php

namespace App\Services;

use App\Models\BanAn;
use App\Models\DatBan;
use Carbon\Carbon;

class BookingAvailabilityService
{
    const SLOT_DURATION = 150; // 2.5 tiếng (15 chuẩn bị + 120 ăn + 15 dọn)

    /**
     * Hàm chính kiểm tra và trả về kết quả
     */
    public function checkAvailability($totalGuest, $gioDen)
    {
        // 1. Kiểm tra chính xác giờ khách chọn
        if ($this->isSlotAvailable($totalGuest, $gioDen)) {
            return ['status' => true];
        }

        // 2. Nếu HẾT BÀN -> Tìm các khung giờ khác trong cùng Ca để gợi ý
        $suggestions = $this->findAlternativeSlots($totalGuest, $gioDen);

        return $this->failMessage($totalGuest, $suggestions);
    }

    /**
     * Hàm phụ: Trả về True/False xem giờ đó có bàn không
     * (Tách logic cũ của bạn vào đây để tái sử dụng)
     */
    public function isSlotAvailable($totalGuest, $timeString)
    {
        $checkTime = Carbon::parse($timeString);
        $requestedEnd = $checkTime->copy()->addMinutes(self::SLOT_DURATION);

        // --- Logic Phân loại bàn (Giữ nguyên) ---
        $minSeat = 0; $maxSeat = 0;
        if ($totalGuest >= 1 && $totalGuest <= 4) { $minSeat = 1; $maxSeat = 4; }
        elseif ($totalGuest >= 5 && $totalGuest <= 8) { $minSeat = 5; $maxSeat = 8; }
        else { $minSeat = 9; $maxSeat = 99; }

        // --- Logic Tổng cung (Giữ nguyên) ---
        $totalTables = BanAn::where('trang_thai', '!=', 'khong_su_dung')
            ->whereNotIn('khu_vuc_id', [5, 9])
            ->whereBetween('so_ghe', [$minSeat, $maxSeat])
            ->count();

        if ($totalTables == 0) return false;

        // --- Logic Tổng cầu - Online (Giữ nguyên logic trùng giờ) ---
        $onlineBookings = DatBan::query()
            ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
            ->where(function ($q) use ($checkTime, $requestedEnd) {
                // Logic giao thoa thời gian: (StartA < EndB) && (EndA > StartB)
                $q->where('gio_den', '<', $requestedEnd)
                  ->whereRaw("DATE_ADD(gio_den, INTERVAL ? MINUTE) > ?", [self::SLOT_DURATION, $checkTime]);
            })
            ->whereRaw('(nguoi_lon + IFNULL(tre_em, 0)) BETWEEN ? AND ?', [$minSeat, $maxSeat])
            ->count();

        // --- Logic Tổng cầu - Walk-in (Giữ nguyên) ---
        $activeWalkin = 0;
        $busyTables = BanAn::where('trang_thai', 'dang_phuc_vu')
            ->whereBetween('so_ghe', [$minSeat, $maxSeat])
            ->whereNotIn('khu_vuc_id', [5, 9])
            ->get();

        foreach ($busyTables as $table) {
            // Giả định bàn đang ăn sẽ ảnh hưởng slot này nếu thời gian chênh lệch < 2.5 tiếng
            if ($checkTime->diffInHours(now()) < 2.5) {
                 $activeWalkin++;
            }
        }

        // Kết luận
        return $totalTables > ($onlineBookings + $activeWalkin);
    }

    /**
     * Hàm mới: Quét các khung giờ lân cận để tìm gợi ý
     */
    private function findAlternativeSlots($totalGuest, $originalTime)
    {
        $original = Carbon::parse($originalTime);
        $availableSlots = [];
        
        // Xác định ca dựa trên giờ khách chọn để giới hạn phạm vi tìm kiếm
        $hour = $original->hour;
        
        // Cấu hình ca giống hệt bên JS View của bạn
        if ($hour < 15) {
            // Ca Trưa: 10:30 -> 14:00
            $startLoop = $original->copy()->setTime(10, 30);
            $endLoop   = $original->copy()->setTime(14, 0);
        } else {
            // Ca Tối: 17:00 -> 22:00
            $startLoop = $original->copy()->setTime(17, 0);
            $endLoop   = $original->copy()->setTime(22, 0);
        }

        // Vòng lặp kiểm tra từng slot 30 phút
        $current = $startLoop->copy();
        
        // Chỉ gợi ý tối đa 3 khung giờ để không bị rối
        while ($current <= $endLoop && count($availableSlots) < 3) {
            
            // 1. Bỏ qua giờ quá khứ (nếu là hôm nay) + Buffer 30p
            if ($current->lt(now()->addMinutes(30))) {
                $current->addMinutes(30);
                continue;
            }

            // 2. Không check lại đúng cái giờ vừa bị trùng (đỡ tốn query)
            if ($current->format('H:i') === $original->format('H:i')) {
                $current->addMinutes(30);
                continue;
            }

            // 3. Tái sử dụng hàm check logic cũ
            if ($this->isSlotAvailable($totalGuest, $current->toDateTimeString())) {
                $availableSlots[] = $current->format('H:i');
            }

            $current->addMinutes(30);
        }

        return $availableSlots;
    }

    /**
     * Tạo thông báo lỗi kèm gợi ý HTML
     */
    private function failMessage($soNguoi, $suggestions = [])
    {
        $msg = "Rất tiếc, nhà hàng đã kín bàn phù hợp cho <b>$soNguoi người</b> vào khung giờ này.";
        
        if (!empty($suggestions)) {
            // Tạo chuỗi gợi ý: 17:30, 19:00...
            $list = implode(', ', $suggestions);
            
            // Format HTML đẹp cho SweetAlert
            $msg .= "<br><br>";
            $msg .= "<div style='background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; border: 1px solid #ffeeba;'>";
            $msg .= "<i class='fa fa-star me-2'></i><b>Gợi ý khung giờ còn trống:</b><br>";
            $msg .= "<span style='font-size: 1.2rem; font-weight: bold; color: #FEA116;'>$list</span>";
            $msg .= "</div>";
        } else {
            $msg .= "<br><br>⚠️ <b>Cả ca này hiện đã kín bàn.</b>";
        }

        $msg .= "<br><br>📞 Hoặc vui lòng gọi Hotline: <b>0909.123.456</b> để nhân viên hỗ trợ xếp bàn khẩn cấp.";

        return ['status' => false, 'message' => $msg];
    }
}