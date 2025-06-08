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
        Schema::create('kpta.konsentrasi_prodi_pd', function (Blueprint $table) {
            $table->uuid('id_konsentrasi_prodi_pd')->primary();
            $table->uuid('id_pd')->nullable();
            $table->foreign('id_pd')
                ->references('id_pd')
                ->on('pdrd.peserta_didik')
                ->onDelete('restrict');
            $table->uuid('id_konsentrasi_prodi')->nullable();
            $table->foreign('id_konsentrasi_prodi')
                ->references('id_konsentrasi_prodi')
                ->on('kpta.konsentrasi_prodi')
                ->onDelete('restrict');
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
        Schema::dropIfExists('kpta.konsentrasi_prodi_pd');
    }
};
