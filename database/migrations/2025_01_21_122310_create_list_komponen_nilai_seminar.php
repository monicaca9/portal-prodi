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
        Schema::create('kpta.list_komponen_nilai_seminar', function (Blueprint $table) {
            $table->uuid('id_list_komponen_nilai')->primary();
            $table->uuid('id_seminar_prodi');
            $table->foreign('id_seminar_prodi')
                ->references('id_seminar_prodi')
                ->on('kpta.seminar_prodi')
                ->onDelete('restrict');
            $table->uuid('id_komponen_nilai');
            $table->foreign('id_komponen_nilai')
                ->references('id_komponen_nilai')
                ->on('kpta.komponen_nilai_seminar')
                ->onDelete('restrict');
            $table->uuid('id_list_kategori_nilai');
            $table->foreign('id_list_kategori_nilai')
                ->references('id_list_kategori_nilai')
                ->on('kpta.list_kategori_nilai_seminar')
                ->onDelete('restrict');
            $table->unsignedTinyInteger('urutan');
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
        Schema::dropIfExists('kpta.list_komponen_nilai_seminar');
    }
};
