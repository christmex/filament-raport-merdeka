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
        Schema::table('subject_descriptions', function (Blueprint $table) {
            $table->boolean('is_english_description')->default(0)->after('predicate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_descriptions', function (Blueprint $table) {
            $table->dropColumn('is_english_description');
        });
    }
};
