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
        Schema::create('kpta.sisa_waktu_penyusunan', function (Blueprint $table) {
            $table->uuid('id_sisa_wkt')->primary();
            $table->tinyInteger('id_jns_seminar');
            $table->foreign('id_jns_seminar')
                ->references('id_jns_seminar')
                ->on('ref.jenis_seminar')
                ->onDelete('restrict');

            $table->uuid('id_reg_pd');
            $table->foreign('id_reg_pd')
                ->references('id_reg_pd')
                ->on('pdrd.reg_pd')
                ->onDelete('restrict');

            $table->dateTime('tgl_mulai')->nullable();
            $table->dateTime('tgl_batas_penyusunan')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
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
        Schema::dropIfExists('kpta.sisa_waktu_penyusunan');
    }
};
