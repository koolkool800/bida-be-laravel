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
        Schema::create('tables', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->unique();
            $table->boolean('is_available')->default(true);
            $table->integer('setting_table_id')->unsigned();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // SET FOREIGN
            $table->foreign('setting_table_id')->references('id')->on('setting_table')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tables');
    }
};
