<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surat_aktif.letter_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('submission_id');
            $table->foreign('submission_id')
                ->references('id')
                ->on('surat_aktif.student_active_letter')
                ->onDelete('cascade');
            $table->string('role');
            $table->string('notes')->nullable();
            $table->string('status')->default('menunggu');
            $table->string('short_code', 10)->unique()->nullable();

            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('soft_delete')->default(0);
            $table->timestamp('last_sync')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surat_aktif.letter_signatures');
    }
};
