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
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->string('hinh_anh')->nullable()->after('trang_thai')->comment('Ảnh đại diện của nhân viên');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nhan_vien', function (Blueprint $table) {
            $table->dropColumn('hinh_anh');
        });
    }
};
