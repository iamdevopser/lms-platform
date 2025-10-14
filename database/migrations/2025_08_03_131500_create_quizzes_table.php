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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'exam', 'assignment'])->default('quiz');
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('passing_score')->default(70); // percentage
            $table->integer('max_attempts')->default(3);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_correct_answers')->default(true);
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['course_id', 'is_active']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
}; 