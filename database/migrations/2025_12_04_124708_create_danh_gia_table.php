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
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->id();
            // Thông tin khách (không liên kết hóa đơn)
            $table->string('ten_khach');
            $table->string('sdt');
            $table->string('email')->nullable();
            $table->string('nghe_nghiep')->nullable()->comment('Nghề nghiệp của khách');
            
            // Nội dung đánh giá
            $table->text('noi_dung');
            $table->unsignedTinyInteger('so_sao')->default(5)->comment('Đánh giá từ 1 đến 5 sao');
            
            // Trạng thái quản lý (Mặc định chờ duyệt)
            $table->enum('trang_thai', ['cho_duyet', 'hien_thi', 'an'])
                  ->default('cho_duyet')
                  ->comment('Trạng thái hiển thị đánh giá');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gia');
    }
};