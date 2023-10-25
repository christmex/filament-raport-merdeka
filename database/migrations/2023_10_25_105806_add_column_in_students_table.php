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
        Schema::table('students', function (Blueprint $table) {
            $table->string('born_place')->after('student_nisn')->nullable();
            $table->date('born_date')->after('student_nisn')->nullable();
            $table->boolean('sex')->after('student_nisn')->nullable();
            $table->string('status_in_family')->after('student_nisn')->nullable();
            $table->tinyInteger('sibling_order_in_family')->after('student_nisn')->nullable();
            $table->string('address')->after('student_nisn')->nullable();
            $table->string('phone')->after('student_nisn')->nullable();
            $table->string('previous_education')->after('student_nisn')->nullable();
            $table->string('father_name')->after('student_nisn')->nullable();
            $table->string('mother_name')->after('student_nisn')->nullable();
            $table->string('parent_address')->after('student_nisn')->nullable();
            $table->string('parent_phone')->after('student_nisn')->nullable();
            $table->string('father_job')->after('student_nisn')->nullable();
            $table->string('mother_job')->after('student_nisn')->nullable();
            $table->string('guardian_name')->after('student_nisn')->nullable();
            $table->string('guardian_phone')->after('student_nisn')->nullable();
            $table->string('guardian_address')->after('student_nisn')->nullable();
            $table->string('guardian_job')->after('student_nisn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('born_place');
            $table->dropColumn('born_date');
            $table->dropColumn('sex');
            $table->dropColumn('status_in_family');
            $table->dropColumn('sibling_order_in_family');
            $table->dropColumn('address');
            $table->dropColumn('phone');
            $table->dropColumn('previous_education');
            $table->dropColumn('father_name');
            $table->dropColumn('mother_name');
            $table->dropColumn('parent_address');
            $table->dropColumn('parent_phone');
            $table->dropColumn('father_job');
            $table->dropColumn('mother_job');
            $table->dropColumn('guardian_name');
            $table->dropColumn('guardian_phone');
            $table->dropColumn('guardian_address');
            $table->dropColumn('guardian_job');
        });
    }
};
