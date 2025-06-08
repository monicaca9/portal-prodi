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
        Schema::create('rps', function (Blueprint $table) {
            $table->uuid('id_rps')->primary();
            $table->foreignUuid('id_sdm');
            $table->text('tujuan_umum');
            $table->text('daftar_pustaka');
            $table->text('evaluasi');
            $table->text('bahan_ajar');
            $table->dateTime('tgl_create');
            $table->uuid('id_creator');
            $table->dateTime('last_update');
            $table->uuid('id_updater');
            $table->tinyInteger('soft_delete');
            $table->dateTime('last_sync');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rps');
    }
};
