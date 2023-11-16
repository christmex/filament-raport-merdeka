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
            $table->string('npsn')->nullable()->after('school_progress_report_date');
            $table->string('nis_nss_nds')->nullable()->after('school_progress_report_date');
            $table->string('telp')->nullable()->after('school_progress_report_date');
            $table->string('postal_code')->nullable()->after('school_progress_report_date');
            $table->string('village')->nullable()->after('school_progress_report_date');
            $table->string('subdistrict')->nullable()->after('school_progress_report_date');
            $table->string('city')->nullable()->after('school_progress_report_date');
            $table->string('province')->nullable()->after('school_progress_report_date');
            $table->string('website')->nullable()->after('school_progress_report_date');
            $table->string('email')->nullable()->after('school_progress_report_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn('npsn');
            $table->dropColumn('nis_nss_nds');
            $table->dropColumn('telp');
            $table->dropColumn('postal_code');
            $table->dropColumn('village');
            $table->dropColumn('subdistrict');
            $table->dropColumn('city');
            $table->dropColumn('province');
            $table->dropColumn('website');
            $table->dropColumn('email');
        });
    }
};
