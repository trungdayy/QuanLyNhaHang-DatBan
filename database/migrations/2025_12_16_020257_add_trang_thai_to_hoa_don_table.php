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
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->enum('trang_thai', ['da_thanh_toan', 'chua_thanh_toan'])
                  ->default('chua_thanh_toan')
                  ->after('phuong_thuc_tt')
                  ->comment('Trạng thái thanh toán: đã thanh toán hoặc chưa thanh toán');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoa_don', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
