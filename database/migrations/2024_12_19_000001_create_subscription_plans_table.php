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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_cycle'); // monthly, yearly, weekly
            $table->integer('trial_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->json('features')->nullable(); // Array of features
            $table->integer('max_courses')->nullable(); // Unlimited if null
            $table->integer('max_students')->nullable(); // Unlimited if null
            $table->boolean('priority_support')->default(false);
            $table->boolean('certificate_creation')->default(false);
            $table->boolean('advanced_analytics')->default(false);
            $table->string('stripe_price_id')->nullable();
            $table->string('stripe_product_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
}; 