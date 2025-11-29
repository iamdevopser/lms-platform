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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained('course_lectures')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->integer('points')->default(100);
            $table->enum('submission_type', ['file', 'text', 'link', 'mixed'])->default('mixed');
            $table->integer('max_file_size')->default(10); // MB
            $table->json('allowed_file_types')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_late_submission')->default(false);
            $table->integer('late_submission_penalty')->default(0);
            $table->boolean('requires_peer_review')->default(false);
            $table->timestamp('peer_review_deadline')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['course_id', 'is_active']);
            $table->index(['lesson_id', 'is_active']);
            $table->index(['due_date', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};

