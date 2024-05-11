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
        Schema::create('don_hang_chi_tiet', function(Blueprint $table) {
            $table->increments('id');
            $table->string('so_luong_san_pham', 255)->unique();
            $table->double('gia_san_pham', 16, 2);
            $table->timestamp('thoi_gian_tao')->useCurrent();
            $table->timestamp('thoi_gian_cap_nhat')->useCurrent()->useCurrentOnUpdate();

            $table->integer('don_hang_id')->unsigned();
            $table->integer('san_pham_id')->unsigned()->nullable();

            // SET FOREIGN
            $table->foreign('san_pham_id')->references('id')->on('san_pham')->onDelete('SET NULL');
            $table->foreign('don_hang_id')->references('id')->on('orders')->onDelete('CASCADE');
        });   

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('don_hang_chi_tiet');
    }
};
