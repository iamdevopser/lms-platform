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
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('widget_type'); // earnings, visits, engagement, performance, etc.
            $table->string('widget_title');
            $table->json('widget_config')->nullable(); // Widget specific configuration
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->integer('width')->default(6); // Bootstrap grid width (1-12)
            $table->integer('height')->default(4); // Height in rows
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_collapsed')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'widget_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
}; 