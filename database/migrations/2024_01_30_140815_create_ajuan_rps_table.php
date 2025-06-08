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
        Schema::create('ajuan_rps', function (Blueprint $table) {
            $table->uuid('id_ajuan_rps')->primary();
            $table->foreignUuid('id_mk');
            $table->foreignUuId('id_sdm');
            $table->foreignUuId('id_rps');
            $table->dateTime('waktu_create');
            $table->tinyInteger('status_ajuan');
            $table->char('jns_ajuan');
            $table->dateTime('waktu_update');
            $table->text('tujuan_umum_awal');
            $table->text('tujuan_umum_baru');
            $table->text('daftar_pustaka_lama');
            $table->text('daftar_pustaka_baru');
            $table->text('evaluasi_lama');
            $table->text('evaluasi_baru');
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
        Schema::dropIfExists('ajuan_rps');
    }
};
