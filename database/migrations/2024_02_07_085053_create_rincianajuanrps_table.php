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
        Schema::create('rincian_ajuan_rps', function (Blueprint $table) {
            $table->uuid('id_rincian_ajuan_rps')->primary();
            $table->foreignUuid('id_ajuan_rps');
            $table->smallInteger('minggu_ke_lama');
            $table->smallInteger('minggu_ke_baru');
            $table->text('tujuan_khusus_lama');
            $table->text('tujuan_khusus_baru');
            $table->text('pokok_bahasan_lama');
            $table->text('pokok_bahasan_baru');
            $table->text('referensi_lama');
            $table->text('referensi_baru');
            $table->text('sub_pokok_bahasan_lama');
            $table->text('sub_pokok_bahasan_baru');
            $table->text('metode_lama');
            $table->text('metode_baru');
            $table->text('media_lama');
            $table->text('media_baru');
            $table->text('aktifitas_penugasan_lama');
            $table->text('aktifitas_penugasan_baru');
            $table->dateTime('tanggal_create_record');
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
        Schema::dropIfExists('rincian_ajuan_rps');
    }
};
