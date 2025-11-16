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
        Schema::table('courses', function (Blueprint $table) {
            // Add new fields for the wizard form
            $table->string('level')->nullable()->after('label'); // beginner, intermediate, advanced
            $table->text('course_goals')->nullable()->after('prerequisites'); // JSON field for goals
            $table->string('image')->nullable()->after('course_image'); // New image field
            // Note: status field already exists in the original migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['level', 'course_goals', 'image']);
        });
    }
};
