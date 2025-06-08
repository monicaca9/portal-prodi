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
        Schema::table('kpta.peran_seminar', function (Blueprint $table) {
            $table->uuid('id_rincian_peran_seminar')->nullable()->after('sk_tugas');
            $table->foreign('id_rincian_peran_seminar')
                ->references('id_rincian_peran_seminar')
                ->on('kpta.rincian_peran_seminar')
                ->onDelete('restrict');

            $table->string('nm_pembimbing_luar_kampus')->nullable();
            $table->string('nm_penguji_luar_kampus')->nullable();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpta.peran_seminar', function (Blueprint $table) {
            $table->dropColumn(['id_rincian_peran_seminar', 'nm_pembimbing_luar_kampus','nm_penguji_luar_kampus', 'stat_ajuan', 'jns_ajuan', 'wkt_update', 'wkt_ajuan']); // Hapus kolom saat rollback
        });
    }
};
