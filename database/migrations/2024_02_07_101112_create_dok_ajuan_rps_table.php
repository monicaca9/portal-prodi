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
        Schema::create('dok_ajuan_rps', function (Blueprint $table) {
            $table->uuid('id_dok_ajuan_rps')->primary();
            $table->foreignUuId('id_dokumen');
            $table->foreignUuId('id_ajuan_rps');
            $table->dateTime('tanggal_create_record');
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
        Schema::dropIfExists('dok_ajuan_rps');
    }
};
