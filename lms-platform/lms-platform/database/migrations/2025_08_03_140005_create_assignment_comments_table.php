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
        Schema::create('assignment_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('comment');
            $table->enum('comment_type', ['general', 'feedback', 'question', 'answer'])->default('general');
            $table->boolean('is_public')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('assignment_comments')->onDelete('cascade');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['submission_id', 'comment_type']);
            $table->index(['user_id']);
            $table->index(['parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_comments');
    }
};










