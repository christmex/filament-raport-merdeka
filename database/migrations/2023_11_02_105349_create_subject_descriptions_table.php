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
        Schema::create('subject_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_user_id')->constrained('subject_users')->cascadeOnDelete();
            $table->foreignId('topic_setting_id')->constrained('topic_settings')->cascadeOnDelete();
            $table->tinyInteger('range_start')->default(0);
            $table->tinyInteger('range_end')->default(0);
            $table->string('topic_name')->nullable();
            $table->longText('description')->nullable();
            $table->string('predicate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_descriptions');
    }
};
