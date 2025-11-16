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
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('submission_text')->nullable();
            $table->json('submission_files')->nullable();
            $table->json('submission_links')->nullable();
            $table->timestamp('submitted_at');
            $table->enum('status', ['submitted', 'graded', 'returned', 'late'])->default('submitted');
            $table->integer('score')->nullable();
            $table->integer('max_score')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->integer('late_penalty')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['assignment_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['assignment_id', 'status']);
            $table->index(['submitted_at']);
            
            // Unique constraint to prevent duplicate submissions
            $table->unique(['assignment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};

