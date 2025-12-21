<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('khu_vuc', function (Blueprint $table) {
            $table->enum('trang_thai', ['dang_su_dung', 'khong_su_dung'])
                  ->default('dang_su_dung')
                  ->after('tang')
                  ->comment('Trạng thái khu vực: đang sử dụng hoặc không sử dụng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khu_vuc', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
