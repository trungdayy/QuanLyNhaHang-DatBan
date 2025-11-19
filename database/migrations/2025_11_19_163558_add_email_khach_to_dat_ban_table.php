<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dat_ban', function (Blueprint $table) {
            $table->string('email_khach')
                  ->nullable()
                  ->after('ten_khach'); // Thêm sau tên khách
        });
    }

    public function down(): void
    {
        Schema::table('dat_ban', function (Blueprint $table) {
            $table->dropColumn('email_khach');
        });
    }
};
