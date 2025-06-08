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
        Schema::create('dok.dok_syarat_seminar', function (Blueprint $table) {
            $table->uuid('id_dok_syarat_seminar')->primary();
            $table->uuid('id_seminar_prodi');
            $table->foreign('id_seminar_prodi')
                ->references('id_seminar_prodi')
                ->on('kpta.seminar_prodi')
                ->onDelete('restrict');

            $table->uuid('id_list_syarat');
            $table->foreign('id_list_syarat')
                ->references('id_list_syarat')
                ->on('kpta.list_syarat_seminar')
                ->onDelete('restrict');

            $table->uuid('id_dok');
            $table->foreign('id_dok')
                ->references('id_dok')
                ->on('dok.dokumen')
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
        Schema::dropIfExists('dok.dok_syarat_seminar');
    }
};
