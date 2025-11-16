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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_taken')->nullable(); // in seconds
            $table->integer('score')->nullable();
            $table->integer('total_points')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->boolean('passed')->nullable();
            $table->json('answers')->nullable(); // Store all answers
            $table->timestamps();
            
            // Indexes
            $table->index(['quiz_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['quiz_id', 'status']);
            
            // Unique constraint to prevent duplicate attempts
            $table->unique(['quiz_id', 'user_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
}; 