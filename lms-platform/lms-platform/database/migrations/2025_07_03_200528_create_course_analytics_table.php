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
        Schema::create('course_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('instructor_id');
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('avg_watch_time')->default(0); // saniye cinsinden
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('no action');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_analytics');
    }
};
