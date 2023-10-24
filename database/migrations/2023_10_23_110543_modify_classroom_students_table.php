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
        Schema::table('student_classrooms', function (Blueprint $table) {
            $table->foreignId('classroom_id')->after('student_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('school_year_id')->after('student_id')->nullable()->constrained('school_years')->cascadeOnDelete();
            $table->foreignId('school_term_id')->after('student_id')->nullable()->constrained('school_terms')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_classrooms', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
            $table->dropForeign(['school_year_id']);
            $table->dropColumn('school_year_id');
            $table->dropForeign(['school_term_id']);
            $table->dropColumn('school_term_id');
        });
    }
};
