<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_masih.still_study_letter', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('student_number');
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('study_program')->nullable();
            $table->string('semester')->nullable();
            $table->string('academic_year')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->text('purpose')->nullable();
            $table->text('parent_name')->nullable();
            $table->string('parent_nip');
            $table->text('parent_grade')->nullable();
            $table->text('parent_job')->nullable();
            $table->text('parent_institution')->nullable();
            $table->text('parent_address')->nullable();
            $table->string('signature')->nullable();
            $table->string('academic_advisor')->nullable();
            $table->string('supporting_document')->nullable();
            $table->string('supporting_document2')->nullable();
            $table->string('status')->default('dibuat');

            $table->uuid('admin_validation_id')->nullable();
            $table->uuid('advisor_signature_id')->nullable();
            $table->uuid('head_of_program_signature_id')->nullable();
            $table->uuid('head_of_department_signature_id')->nullable();
            $table->uuid('letter_number')->nullable();

            $table->uuid('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('soft_delete')->default(0);
            $table->timestamp('last_sync')->nullable();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masih.still_study_letter');
    }
};
