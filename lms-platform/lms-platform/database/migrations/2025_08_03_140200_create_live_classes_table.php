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
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->integer('duration')->default(60); // in minutes
            $table->integer('max_participants')->default(50);
            $table->integer('current_participants')->default(0);
            $table->enum('status', ['scheduled', 'live', 'ended', 'cancelled'])->default('scheduled');
            $table->string('meeting_url')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->string('recording_url')->nullable();
            $table->boolean('is_recording_enabled')->default(true);
            $table->boolean('is_chat_enabled')->default(true);
            $table->boolean('is_screen_sharing_enabled')->default(true);
            $table->boolean('is_polling_enabled')->default(true);
            $table->boolean('is_whiteboard_enabled')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['course_id', 'status']);
            $table->index(['instructor_id', 'status']);
            $table->index(['start_time', 'status']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};










