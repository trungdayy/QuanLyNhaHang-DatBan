<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\DatBan;
use Carbon\Carbon;

// 1. Định nghĩa command
Artisan::command('booking:update-tables', function () {
    $bookings = DatBan::whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan'])->get();

    foreach ($bookings as $booking) {
        $ban = $booking->banAn;
        $gioDen = Carbon::parse($booking->gio_den);

        if (now()->greaterThan($gioDen->copy()->addMinutes(30)) && $booking->trang_thai == 'cho_xac_nhan') {
            $booking->update(['trang_thai' => 'huy']);
            $ban->update(['status' => 'available']);
            continue;
        }

        if (now()->between($gioDen, $gioDen->copy()->addHours(2))) {
            $ban->update(['status' => 'dang_su_dung']);
        }

        if (now()->greaterThan($gioDen->copy()->addHours(2))) {
            $booking->update(['trang_thai' => 'hoan_tat']);
            $ban->update(['status' => 'available']);
        }
    }

    $this->info('Cập nhật trạng thái bàn xong.');
});

// 2. Lên lịch chạy command
return function (Schedule $schedule) {
    $schedule->command('booking:update-tables')->everyMinute();
};
