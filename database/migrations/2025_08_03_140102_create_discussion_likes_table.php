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
        Schema::create('discussion_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('discussion_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('reply_id')->nullable()->constrained('discussion_replies')->onDelete('cascade');
            $table->enum('type', ['like', 'dislike', 'helpful'])->default('like');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'discussion_id']);
            $table->index(['user_id', 'reply_id']);
            $table->index(['discussion_id', 'type']);
            $table->index(['reply_id', 'type']);
            
            // Ensure either discussion_id or reply_id is set, but not both
            $table->check('(discussion_id IS NOT NULL AND reply_id IS NULL) OR (discussion_id IS NULL AND reply_id IS NOT NULL)');
            
            // Unique constraint to prevent duplicate likes
            $table->unique(['user_id', 'discussion_id', 'type'], 'unique_discussion_like');
            $table->unique(['user_id', 'reply_id', 'type'], 'unique_reply_like');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_likes');
    }
};










