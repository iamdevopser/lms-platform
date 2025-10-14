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
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['general', 'question', 'announcement', 'help'])->default('general');
            $table->enum('status', ['open', 'closed', 'pinned', 'locked'])->default('open');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('reply_count')->default(0);
            $table->timestamp('last_reply_at')->nullable();
            $table->foreignId('last_reply_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['course_id', 'status']);
            $table->index(['course_id', 'type']);
            $table->index(['user_id']);
            $table->index(['is_pinned', 'created_at']);
            $table->index(['last_reply_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};










