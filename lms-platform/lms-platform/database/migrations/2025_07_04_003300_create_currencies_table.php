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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // USD, EUR, TRY, etc.
            $table->string('name'); // US Dollar, Euro, Turkish Lira
            $table->string('symbol', 5); // $, €, ₺
            $table->decimal('exchange_rate', 10, 6)->default(1.000000);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('decimal_places')->default(2);
            $table->string('position', 10)->default('left'); // left, right
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
