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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();
            
            // Aynı kullanıcı aynı kursu birden fazla kez wishlist'e ekleyemez
            $table->unique(['user_id', 'course_id']);
            
            // Indexes for better performance
            $table->index(['user_id']);
            $table->index(['course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
}; 