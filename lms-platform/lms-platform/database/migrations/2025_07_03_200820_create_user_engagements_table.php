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
        Schema::create('user_engagements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('instructor_id');
            $table->string('engagement_type'); // ör: comment, quiz, complete, like
            $table->string('engagement_value')->nullable(); // ör: skor, puan, metin
            $table->date('date');
            $table->text('meta')->nullable(); // json yerine text kullanıyoruz
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('no action');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_engagements');
    }
};
