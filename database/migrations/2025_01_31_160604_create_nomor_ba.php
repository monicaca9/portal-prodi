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
        Schema::create('kpta.nomor_ba', function (Blueprint $table) {
            $table->uuid('id_no_ba')->primary();
            $table->uuid('id_sms');
            $table->foreign('id_sms')
                ->references('id_sms')
                ->on('pdrd.sms')
                ->onDelete('restrict');
            $table->string('nm_akt_ba')->nullable();
            $table->unsignedTinyInteger('no_ba_awal')->nullable();
            $table->unsignedTinyInteger('no_ba_terbaru')->nullable();
            $table->string('kode_ba')->nullable();
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
        Schema::dropIfExists('kpta.nomor_ba');
    }
};
