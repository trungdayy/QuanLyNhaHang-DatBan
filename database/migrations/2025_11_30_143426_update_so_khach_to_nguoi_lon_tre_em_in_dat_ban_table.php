<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dat_ban', function (Blueprint $table) {
            // Xóa cột so_khach cũ
            $table->dropColumn('so_khach');

            // Thêm 2 cột mới
            $table->integer('nguoi_lon')->default(1)->after('sdt_khach');
            $table->integer('tre_em')->default(0)->after('nguoi_lon');
        });
    }

    public function down(): void
    {
        Schema::table('dat_ban', function (Blueprint $table) {
            // Xóa 2 cột mới
            $table->dropColumn(['nguoi_lon', 'tre_em']);

            // Khôi phục cột so_khach
            $table->integer('so_khach')->after('sdt_khach');
        });
    }
};
