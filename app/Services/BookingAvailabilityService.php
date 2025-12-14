<?php

namespace App\Services;

use App\Models\BanAn;
use App\Models\DatBan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Thêm dòng này

class BookingAvailabilityService
{
    const PREPARE_TIME = 15;
    const DINING_TIME  = 120;
    const CLEAN_TIME   = 15;

    // Tham số $totalGuest là Tổng (Người lớn + Trẻ em) được truyền từ Controller
    public function checkAvailability($totalGuest, $gioDen)
    {
        $checkTime = Carbon::parse($gioDen);
        $blockStart = $checkTime->copy()->subMinutes(self::PREPARE_TIME);
        $blockEnd   = $checkTime->copy()->addMinutes(self::DINING_TIME + self::CLEAN_TIME);

        // 1. Phân nhóm bàn dựa trên TỔNG SỐ KHÁCH
        $minSeat = 0; $maxSeat = 0;

        if ($totalGuest >= 1 && $totalGuest <= 4) {
            $minSeat = 1; $maxSeat = 4;
        } 
        elseif ($totalGuest >= 5 && $totalGuest <= 8) {
            $minSeat = 5; $maxSeat = 8;
        } 
        else {
            $minSeat = 9; $maxSeat = 99;
        }

        // 2. Tính Tổng Cung (Capacity)
        $totalBlocks = BanAn::where('trang_thai', '!=', 'khong_su_dung')
            ->whereNotIn('khu_vuc_id', [5, 9])
            ->whereBetween('so_ghe', [$minSeat, $maxSeat])
            ->count();

        if ($totalBlocks == 0) {
            return $this->getSuggestionMessage($totalGuest, $checkTime);
        }

        // 3. Tính Tổng Cầu (Used) - Đếm dựa trên (nguoi_lon + tre_em)
        $usedBlocks = 0;

        // A. Đếm đơn ONLINE
        $onlineBookings = DatBan::query()
            ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
            ->where(function ($q) use ($blockStart, $blockEnd) {
                $q->whereRaw("DATE_SUB(gio_den, INTERVAL ? MINUTE) < ?", [self::PREPARE_TIME, $blockEnd])
                  ->whereRaw("DATE_ADD(gio_den, INTERVAL ? MINUTE) > ?", [self::DINING_TIME + self::CLEAN_TIME, $blockStart]);
            })
            // [QUAN TRỌNG] Đếm tổng người lớn + trẻ em
            ->whereRaw('(nguoi_lon + IFNULL(tre_em, 0)) BETWEEN ? AND ?', [$minSeat, $maxSeat])
            // Chỉ đếm đơn Online hoặc chưa gán bàn
            ->where(function($q) {
                $q->whereNull('ban_id')
                  ->orWhereHas('banAn', function($subQ) {
                      $subQ->whereNotIn('khu_vuc_id', [5, 9]);
                  });
            })
            ->count();
        
        $usedBlocks += $onlineBookings;

        // B. Đếm Walk-in
        $now = Carbon::now();
        $futureReleaseTime = $now->copy()->addMinutes(self::DINING_TIME + self::CLEAN_TIME);

        if ($blockStart < $futureReleaseTime) {
            $activeWalkinTables = BanAn::whereBetween('so_ghe', [$minSeat, $maxSeat])
                ->whereNotIn('khu_vuc_id', [5, 9])
                ->where('trang_thai', 'dang_phuc_vu')
                ->get();

            foreach ($activeWalkinTables as $table) {
                $hasActiveBooking = DatBan::where('ban_id', $table->id)
                    ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
                    ->where('gio_den', '<', $blockEnd)
                    ->exists();

                if (!$hasActiveBooking) {
                    $usedBlocks++;
                }
            }
        }

        // 4. Kết luận
        if ($totalBlocks > $usedBlocks) {
            return ['status' => true];
        }

        return $this->getSuggestionMessage($totalGuest, $checkTime);
    }

    private function getSuggestionMessage($soNguoi, $requestTime)
    {
        $msg = "Rất tiếc, nhà hàng đã kín bàn phù hợp cho $soNguoi người.";
        $msg .= "\n💡 Gợi ý: Vui lòng chọn khung giờ khác.";
        $msg .= "\n📞 Hoặc vui lòng gọi Hotline: **0909.123.456** để nhân viên hỗ trợ xếp bàn khẩn cấp.";
        return ['status' => false, 'message' => $msg];
    }
}