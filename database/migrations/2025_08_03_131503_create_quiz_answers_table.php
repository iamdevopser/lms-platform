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
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_question_id')->constrained()->onDelete('cascade');
            $table->json('user_answer')->nullable(); // Store user's answer
            $table->boolean('is_correct')->nullable();
            $table->integer('points_earned')->default(0);
            $table->text('feedback')->nullable(); // Instructor feedback for essay questions
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['quiz_attempt_id', 'quiz_question_id']);
            $table->index(['quiz_question_id', 'is_correct']);
            
            // Unique constraint to prevent duplicate answers
            $table->unique(['quiz_attempt_id', 'quiz_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
}; 