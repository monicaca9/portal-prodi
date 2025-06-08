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
        Schema::create('kpta.skor_per_komponen', function (Blueprint $table) {
            $table->uuid('id_skor_komponen')->primary();
            $table->uuid('id_list_komponen_nilai');
            $table->foreign('id_list_komponen_nilai')
                ->references('id_list_komponen_nilai')
                ->on('kpta.list_komponen_nilai_seminar')
                ->onDelete('restrict');
            $table->uuid('id_peran_dosen_pendaftar');
            $table->foreign('id_peran_dosen_pendaftar')
                ->references('id_peran_dosen_pendaftar')
                ->on('kpta.peran_dosen_pendaftar')
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
        Schema::dropIfExists('kpta.skor_per_komponen');
    }
};
