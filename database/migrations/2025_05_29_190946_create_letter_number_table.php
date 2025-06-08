<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_aktif.letter_number', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('letter_id');
            $table->string('code');
            $table->year('year');
            $table->integer('number');
            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('soft_delete')->default(0);
            $table->timestamp('last_sync')->nullable();
            $table->unique(['year', 'code', 'number']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('surat_aktif.letter_number');
    }
};
