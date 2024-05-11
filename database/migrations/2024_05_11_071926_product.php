<?php

use App\Enums\ProductType;
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
        Schema::create('san_pham', function(Blueprint $table) {
            $table->increments('id');
            $table->string('hinh_anh_url', 3000);
            $table->string('ten_san_pham', 255);
            $table->double('gia_san_pham', 16, 2);
            $table->enum('loai_san_pham', ProductType::getValues());
            $table->timestamp('thoi_gian_tao')->useCurrent();
            $table->timestamp('thoi_gian_cap_nhat')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('san_pham');
    }
};
