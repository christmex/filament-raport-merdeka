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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('assessment_method_setting_id')->constrained('assessment_method_settings')->cascadeOnDelete();
            $table->foreignId('topic_setting_id')->constrained('topic_settings')->cascadeOnDelete();
            $table->integer('grading')->nullable();
            $table->foreignId('subject_user_id')->constrained('subject_users')->cascadeOnDelete();
            $table->string('topic_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
