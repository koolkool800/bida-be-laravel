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
        Schema::table('don_hang_chi_tiet', function (Blueprint $table) {
            $table->dropUnique('don_hang_chi_tiet_so_luong_san_pham_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_hang_chi_tiet', function (Blueprint $table) {
            $table->unique('so_luong_san_pham', 255);
        });
    }
};
