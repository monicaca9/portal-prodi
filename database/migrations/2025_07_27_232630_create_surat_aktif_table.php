<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_aktif.surat_aktif', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('npm');
            $table->string('nama');
            $table->string('jurusan');
            $table->string('prodi');
            $table->string('semester');
            $table->string('thn_akademik');
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->text('tujuan')->nullable();
            $table->string('validasi')->nullable();
            $table->string('dosen_pa')->nullable();
            $table->string('dokumen')->nullable();
            $table->string('status')->default('dibuat');

            $table->uuid('id_validasi_admin')->nullable();
            $table->uuid('id_validasi_pa')->nullable();
            $table->uuid('id_validasi_kaprodi')->nullable();
            $table->uuid('id_validasi_kajur')->nullable();
            $table->uuid('no_surat')->nullable();

            $table->uuid('id_creator')->nullable();
            $table->timestamp('tgl_create')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->boolean('soft_delete')->default(0);
            $table->timestamp('last_sync')->nullable();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_aktif.surat_aktif');
    }
};
