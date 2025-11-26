<?php

namespace App\Helpers;

class BookingHelper
{
    /**
     * Tính tiền cọc dựa vào giá combo và số khách
     *
     * @param float|int $comboPrice Giá 1 combo/người
     * @param int $soKhach Số khách
     * @return float Số tiền cọc
     */
    public static function calculateDeposit($comboPrice, $soKhach)
    {
        // Tỷ lệ cọc theo số khách
        if ($soKhach <= 1) $percent = 0.10;
        elseif ($soKhach == 2) $percent = 0.15;
        elseif ($soKhach == 3) $percent = 0.20;
        elseif ($soKhach == 4) $percent = 0.25;
        elseif ($soKhach == 5) $percent = 0.30;
        elseif ($soKhach == 6) $percent = 0.35;
        elseif ($soKhach == 7 || $soKhach == 8) $percent = 0.40;
        else $percent = 0.50;

        return round($comboPrice * $soKhach * $percent); // làm tròn về đồng
    }
}
