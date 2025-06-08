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
        Schema::create('kpta.nomor_ba_seminar', function (Blueprint $table) {
            $table->uuid('id_no_ba_seminar')->primary();
            $table->uuid('id_seminar_prodi');
            $table->foreign('id_seminar_prodi')
                ->references('id_seminar_prodi')
                ->on('kpta.seminar_prodi')
                ->onDelete('restrict');
            $table->uuid('id_no_ba');
            $table->foreign('id_no_ba')
                ->references('id_no_ba')
                ->on('kpta.nomor_ba')
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
        Schema::dropIfExists('kpta.nomor_ba_seminar');
    }
};
