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
        Schema::create('discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('discussion_replies')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_solution')->default(false);
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['discussion_id', 'parent_id']);
            $table->index(['user_id']);
            $table->index(['is_solution']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
    }
};










