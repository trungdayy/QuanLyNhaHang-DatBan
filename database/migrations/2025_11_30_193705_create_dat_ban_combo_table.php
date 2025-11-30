<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tạo bảng mới
        Schema::create('dat_ban_combo', function (Blueprint $table) {
            $table->id();
            // Liên kết sang bảng dat_ban, xóa đặt bàn thì xóa luôn chi tiết này
            $table->foreignId('dat_ban_id')->constrained('dat_ban')->onDelete('cascade');
            // Liên kết sang bảng combo, xóa combo thì xóa luôn chi tiết này
            $table->foreignId('combo_id')->constrained('combo_buffet')->onDelete('cascade');
            
            $table->integer('so_luong')->default(1);
            $table->timestamps();
        });

        // 2. Xóa cột cũ ở bảng dat_ban
        Schema::table('dat_ban', function (Blueprint $table) {
            if (Schema::hasColumn('dat_ban', 'combo_id')) {
                // Phải xóa khóa ngoại trước khi xóa cột
                $table->dropForeign(['combo_id']); 
                $table->dropColumn('combo_id');
            }
        });
    }

    public function down(): void
    {
        // Hoàn tác lại nếu rollback
        Schema::dropIfExists('dat_ban_combo');

        Schema::table('dat_ban', function (Blueprint $table) {
            $table->foreignId('combo_id')->nullable()->constrained('combo_buffet')->onDelete('set null');
        });
    }
};