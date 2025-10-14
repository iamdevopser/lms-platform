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
        Schema::create('assignment_peer_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->json('criteria_scores')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamps();
            
            // Indexes
            $table->index(['submission_id', 'reviewer_id']);
            $table->index(['reviewer_id', 'status']);
            
            // Unique constraint to prevent duplicate reviews
            $table->unique(['submission_id', 'reviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_peer_reviews');
    }
};










