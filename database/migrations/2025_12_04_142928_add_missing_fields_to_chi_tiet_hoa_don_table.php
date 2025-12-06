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
        Schema::table('chi_tiet_hoa_don', function (Blueprint $table) {
            // Thêm các trường còn thiếu để lưu đầy đủ thông tin thanh toán
            $table->integer('nguoi_lon')->nullable()->after('so_khach')->comment('Số người lớn');
            $table->integer('tre_em')->nullable()->after('nguoi_lon')->comment('Số trẻ em');
            $table->decimal('tong_tien_mon_goi_them', 12, 2)->default(0.00)->after('tong_tien_combo')->comment('Tổng tiền món gọi thêm');
            $table->decimal('tong_tien_sau_voucher', 12, 2)->default(0.00)->after('tien_giam_voucher')->comment('Tổng tiền sau khi áp dụng voucher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_hoa_don', function (Blueprint $table) {
            $table->dropColumn(['nguoi_lon', 'tre_em', 'tong_tien_mon_goi_them', 'tong_tien_sau_voucher']);
        });
    }
};