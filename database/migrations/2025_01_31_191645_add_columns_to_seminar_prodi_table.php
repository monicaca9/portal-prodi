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
        Schema::table('kpta.seminar_prodi', function (Blueprint $table) {
            $table->uuid('id_mk')->nullable();
            $table->foreign('id_mk')
                ->references('id_mk')
                ->on('pdrd.matkul')
                ->onDelete('restrict');
            // $table->uuid('id_no_ba')->nullable();
            // $table->foreign('id_no_ba')
            //     ->references('id_no_ba')
            //     ->on('kpta.nomor_berita_acara')
            //     ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpta.seminar_prodi', function (Blueprint $table) {
            $table->dropColumn(['id_mk']); 
        });
    }
};
