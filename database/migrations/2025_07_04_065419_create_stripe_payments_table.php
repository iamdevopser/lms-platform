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
        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('stripe_customer_id')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status'); // succeeded, pending, failed, canceled
            $table->string('payment_method_type')->nullable(); // card, bank_transfer, etc.
            $table->json('payment_method_details')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['stripe_payment_intent_id']);
            $table->index(['stripe_customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_payments');
    }
};
