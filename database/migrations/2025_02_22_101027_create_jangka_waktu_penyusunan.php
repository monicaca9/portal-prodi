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
        Schema::create('kpta.jangka_waktu_penyusunan', function (Blueprint $table) {
            $table->uuid('id_jangka_wkt')->primary();
            $table->tinyInteger('id_jns_seminar');
            $table->foreign('id_jns_seminar')
                ->references('id_jns_seminar')
                ->on('ref.jenis_seminar')
                ->onDelete('restrict');

            $table->tinyInteger('id_jenj_didik');
            $table->foreign('id_jenj_didik')
                ->references('id_jenj_didik')
                ->on('ref.jenjang_pendidikan')
                ->onDelete('restrict');

            $table->uuid('id_sp');
            $table->foreign('id_sp')
                ->references('id_sp')
                ->on('pdrd.satuan_pendidikan')
                ->onDelete('restrict');

            $table->tinyInteger('durasi_penyusunan')->nullable();
            $table->tinyInteger('durasi_perpanjangan')->nullable();
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
        Schema::dropIfExists('kpta.jangka_waktu_penyusunan');
    }
};
