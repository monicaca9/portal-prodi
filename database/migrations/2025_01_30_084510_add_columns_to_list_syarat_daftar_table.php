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
        Schema::table('kpta.list_syarat_daftar', function (Blueprint $table) {
            $table->string('ket_periksa')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpta.list_syarat_daftar', function (Blueprint $table) {
            $table->dropColumn('ket_periksa'); 

        });
    }
};
