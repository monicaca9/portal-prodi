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
        Schema::create('kpta.avg_skor_kategori', function (Blueprint $table) {
            $table->uuid('id_avg_skor_kategori')->primary();
            $table->uuid('id_peran_dosen_pendaftar');
            $table->foreign('id_peran_dosen_pendaftar')
                ->references('id_peran_dosen_pendaftar')
                ->on('kpta.peran_dosen_pendaftar')
                ->onDelete('restrict');
            $table->uuid('id_daftar_seminar')->nullable();
            $table->foreign('id_daftar_seminar')
                ->references('id_daftar_seminar')
                ->on('kpta.pendaftaran_seminar')
                ->onDelete('restrict');
            $table->decimal('skor', 6, 2)->nullable();
            $table->dateTime('tgl_create');
            $table->uuid('id_creator');
            $table->dateTime('last_update');
            $table->uuid('id_updater');
            $table->tinyInteger('soft_delete')->default(0);
            $table->dateTime('last_sync');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpta.avg_skor_kategori');
    }
};
