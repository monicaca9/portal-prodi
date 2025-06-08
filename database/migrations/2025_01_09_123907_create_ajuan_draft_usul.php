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
        Schema::create('validasi.ajuan_draft_usul', function (Blueprint $table) {
            $table->uuid('id_ajuan_draft_usul')->primary();
            $table->uuid('id_pd'); 
            $table->foreign('id_pd') 
                ->references('id_pd')
                ->on('pdrd.peserta_didik') 
                ->onDelete('restrict'); 
            
            $table->unsignedBigInteger('id_jns_seminar'); 
            $table->foreign('id_jns_seminar') 
                ->references('id_jns_seminar')
                ->on('ref.jenis_seminar') 
                ->onDelete('restrict'); 

            $table->string('judul_draft_usul_lama')->nullable();
            $table->string('judul_draft_usul_baru')->nullable();
            $table->text('keterangan')->nullable();
            $table->tinyInteger('stat_ajuan');
            $table->char('jns_ajuan');
            $table->dateTime('wkt_create')->nullable();
            $table->dateTime('wkt_update')->nullable();
            $table->dateTime('wkt_ajuan')->nullable();
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
        Schema::dropIfExists('validasi.ajuan_draft_usul');
    }
};
