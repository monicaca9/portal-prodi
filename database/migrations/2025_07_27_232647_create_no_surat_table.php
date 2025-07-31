<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_aktif.no_surat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode');
            $table->year('tahun');
            $table->integer('nomor');
            $table->uuid('id_creator')->nullable();
            $table->timestamp('tgl_create')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->boolean('soft_delete')->default(0);
            $table->timestamp('last_sync')->nullable();
            $table->unique(['tahun', 'kode', 'nomor']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('surat_aktif.no_surat');
    }
};