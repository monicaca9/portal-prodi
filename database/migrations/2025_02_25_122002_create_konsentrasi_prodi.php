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
        Schema::create('kpta.konsentrasi_prodi', function (Blueprint $table) {
            $table->uuid('id_konsentrasi_prodi')->primary();
            $table->string('nm_konsentrasi_prodi')->nullable();
            $table->uuid('id_sms');
            $table->foreign('id_sms')
                ->references('id_sms')
                ->on('pdrd.sms')
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
        Schema::dropIfExists('kpta.konsentrasi_prodi');
    }
};
