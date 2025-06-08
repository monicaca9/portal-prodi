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
        Schema::create('rps_minggu_mk', function (Blueprint $table) {
            $table->uuid('id_rps_minggu_mk')->primary();
            $table->foreignUuId('id_mk');
            $table->integer('minggu_ke');
            $table->text('sub_cpmk');
            $table->text('bahan_kajian');
            $table->text('pengalaman_belajar');
            $table->text('estimasi_waktu');
            $table->text('kriteria_dan_bentuk');
            $table->text('indikator');
            $table->boolean('is_utsOrUas');
            $table->tinyInteger('soft_delete');
            $table->dateTime('last_sync'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rps_minggu_mk');
    }
};
