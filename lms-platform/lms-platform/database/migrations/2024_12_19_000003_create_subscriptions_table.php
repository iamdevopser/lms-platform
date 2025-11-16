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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->string('stripe_subscription_id')->unique()->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('status'); // active, canceled, past_due, unpaid, trialing
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_cycle'); // monthly, yearly, weekly
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['stripe_subscription_id']);
            $table->index(['stripe_customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
}; 