<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stripe;

class StripeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stripe::updateOrCreate(
            ['id' => 1],
            [
                'publish_key' => env('STRIPE_KEY', 'pk_test_your_stripe_public_key'),
                'secret_key' => env('STRIPE_SECRET', 'sk_test_your_stripe_secret_key'),
            ]
        );
    }
}

