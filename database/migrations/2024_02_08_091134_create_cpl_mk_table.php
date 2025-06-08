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
        Schema::create('cpl_mk', function (Blueprint $table) {
            $table->uuid('id_cpl_mk')->primary();
            $table->foreignId('id_cpl');
            $table->foreignUuId('id_sdm');
            $table->string('nm_cpl');
            $table->text('desc_cpl');
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
        Schema::dropIfExists('cpl_mk');
    }
};
