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
        Schema::create('validasi.ver_ajuan_draft_usul', function (Blueprint $table) {
            $table->uuid('id_ver_ajuan')->primary();
            $table->uuid('id_ajuan_draft_usul')->nullable();
            $table->foreign('id_ajuan_draft_usul')
                ->references('id_ajuan_draft_usul')
                ->on('validasi.ajuan_draft_usul')
                ->onDelete('restrict');

            $table->uuid('id_role_pengguna')->nullable();
            $table->foreign('id_role_pengguna')
                ->references('id_role_pengguna')
                ->on('man_akses.role_pengguna')
                ->onDelete('restrict');

            $table->string('nm_verifikator')->nullable();
            $table->dateTime('wkt_mulai_ver')->nullable();
            $table->dateTime('wkt_selesai_ver')->nullable();
            $table->char('status_periksa', 1)->default('N');
            $table->string('ket_periksa')->nullable();
            $table->integer('verifikasi_ke')->default(1);
            $table->char('level_ver')->default('1');
            $table->char('stat_ajuan_sebelum')->default('0');
            $table->char('stat_ajuan_sesudah')->default('0');

            $table->uuid('id_ver_ajuan_sebelum')->nullable();
            $table->dateTime('tgl_create');
            $table->uuid('id_creator');
            $table->dateTime('last_update');
            $table->uuid('id_updater');
            $table->tinyInteger('soft_delete')->default(0);
            $table->dateTime('last_sync');  

        });

        Schema::table('validasi.ver_ajuan_draft_usul', function (Blueprint $table) {
            $table->foreign('id_ver_ajuan_sebelum')
                  ->references('id_ver_ajuan')
                  ->on('validasi.ver_ajuan_draft_usul')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi.ver_ajuan_draft_usul');
    }
};
