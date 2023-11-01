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
        Schema::table('school_settings', function (Blueprint $table) {
            $table->tinyInteger('sumatif_avg')->nullable()->after('school_progress_report_date');
            $table->tinyInteger('pas_avg')->nullable()->after('school_progress_report_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn('sumatif_avg');
            $table->dropColumn('pas_avg');
        });
    }
};
