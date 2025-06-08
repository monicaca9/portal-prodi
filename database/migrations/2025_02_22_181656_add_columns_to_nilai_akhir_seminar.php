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
        Schema::table('kpta.nilai_akhir_seminar', function (Blueprint $table) {
            $table->tinyInteger('a_valid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpta.nilai_akhir_seminar', function (Blueprint $table) {
            $table->dropColumn(['a_valid']); // Hapus kolom saat rollback
            
        });
    }
};
