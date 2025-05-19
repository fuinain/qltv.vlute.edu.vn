<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dm_bao_cao', function (Blueprint $table) {
            $table->id('id_dm_bao_cao');
            $table->string('ten_bao_cao');
        });

        // Thêm một số loại báo cáo mặc định
        DB::table('dm_bao_cao')->insert([
            ['ten_bao_cao' => 'Thống kê học sinh đăng ký thẻ thư viện'],
            ['ten_bao_cao' => 'Thống kê sách đang mượn'],
            ['ten_bao_cao' => 'Thống kê sách đã trả'],
            ['ten_bao_cao' => 'Thống kê danh sách quá hạn']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dm_bao_cao');
    }
}; 