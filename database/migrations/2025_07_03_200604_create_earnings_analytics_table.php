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
        Schema::create('earnings_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->date('date');
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_type')->nullable();
            $table->integer('order_count')->default(0);
            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings_analytics');
    }
};
