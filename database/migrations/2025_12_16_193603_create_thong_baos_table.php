<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('thong_bao', function (Blueprint $table) {
        $table->id();
        $table->string('loai')->default('goi_phuc_vu'); // Loại: goi_phuc_vu, mon_moi, v.v.
        $table->text('noi_dung'); // VD: "Bàn A1 gọi phục vụ"
        $table->foreignId('dat_ban_id')->nullable()->constrained('dat_ban')->onDelete('cascade');
        $table->boolean('da_xem')->default(false); // Trạng thái đã xử lý chưa
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thong_baos');
    }
};
