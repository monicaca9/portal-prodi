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
        Schema::table('kpta.total_skor_kategori', function (Blueprint $table) {
            $table->uuid('id_daftar_seminar')->nullable();
            $table->foreign('id_daftar_seminar')
                ->references('id_daftar_seminar')
                ->on('kpta.pendaftaran_seminar')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpta.total_skor_kategori', function (Blueprint $table) {
            $table->dropColumn(['id_daftar_seminar']); 
        });
    }
};
