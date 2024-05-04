<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**trash:///%5Cmedia%5Cduynguyen%5CDATA2%5C.Trash-1000%5Cfiles%5C2014_10_12_000000_create_users_table.php
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function(Blueprint $table) {
            $table->increments('id');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->float('current_price')->nullable();
            $table->float('total_price')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('table_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            // SET FOREIGN
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('orders');
    }
};
