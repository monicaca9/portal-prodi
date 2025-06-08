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
        Schema::create('matkul_rps', function (Blueprint $table) {
            $table->uuid('id_matkul_rps')->primary();
            $table->foreignUuid('id_mk');
            $table->foreignUuid('id_pengesahan');
            $table->foreignUuid('id_rps');
            $table->tinyInteger('a_aktif');
            $table->dateTime('waktu_aktif');
            $table->dateTime('tgl_create');
            $table->uuid('id_creator');
            $table->dateTime('last_update');
            $table->uuid('id_updater');
            $table->tinyInteger('soft_delete');
            $table->dateTime('last_sync');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matkul_rps');
    }
};
