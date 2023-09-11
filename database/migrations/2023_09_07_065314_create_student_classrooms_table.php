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
        Schema::create('student_classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('homeroom_teacher_id')->constrained('homeroom_teachers')->cascadeOnDelete();
            // $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            // $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            // $table->foreignId('school_term_id')->constrained('school_terms')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_classrooms');
    }
};
