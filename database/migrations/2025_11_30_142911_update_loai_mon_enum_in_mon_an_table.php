<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cập nhật cột loai_mon
        Schema::table('mon_an', function (Blueprint $table) {
            // Tùy vào DB, với MySQL có thể drop rồi add lại enum
            $table->dropColumn('loai_mon');
        });

        Schema::table('mon_an', function (Blueprint $table) {
            $table->enum('loai_mon', [
                'Sống',
                'Chín',
                'Nướng',
                'Xào / Luộc',
                'Bánh ngọt',
                'Trái cây',
                'Nước có ga',
                'Nước không ga',
                'Trà / Cà phê'
            ])->nullable()->comment('Loại món theo danh mục, tình trạng chế biến hoặc tráng miệng / đồ uống')->after('thoi_gian_che_bien');
        });
    }

    public function down(): void
    {
        // Khôi phục lại enum cũ nếu rollback
        Schema::table('mon_an', function (Blueprint $table) {
            $table->dropColumn('loai_mon');
        });

        Schema::table('mon_an', function (Blueprint $table) {
            $table->enum('loai_mon', [
                'Khai vị',
                'Món chính',
                'Tráng miệng',
                'Đồ uống'
            ])->nullable()->comment('Phân loại món theo lượt ăn (course)')->after('thoi_gian_che_bien');
        });
    }
};
