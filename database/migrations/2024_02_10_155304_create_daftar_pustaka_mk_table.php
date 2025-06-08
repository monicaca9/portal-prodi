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
        Schema::create('daftar_pustaka_mk', function (Blueprint $table) {
            $table->uuid('id_daftar_pustaka_mk')->primary();
            $table->foreignUuId('id_mk');
            $table->text('daftar_pustaka');
            $table->tinyInteger('soft_delete');
            $table->dateTime('last_sync'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_pustaka_mk');
    }
};
