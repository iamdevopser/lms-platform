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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'single_choice', 'true_false', 'fill_blank', 'essay'])->default('multiple_choice');
            $table->json('options')->nullable(); // For multiple choice questions
            $table->json('correct_answers')->nullable(); // Array of correct answers
            $table->text('explanation')->nullable(); // Explanation for the answer
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['quiz_id', 'order']);
            $table->index(['quiz_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
}; 