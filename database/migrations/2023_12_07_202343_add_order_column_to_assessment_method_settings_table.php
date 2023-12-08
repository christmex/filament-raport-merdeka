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
        Schema::table('assessment_method_settings', function (Blueprint $table) {
            $table->tinyInteger('order')->default(1)->after('assessment_method_setting_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_method_settings', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
