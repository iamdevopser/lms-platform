<?php

return [
    'stripe_pk' => env('STRIPE_KEY', env('STRIPE_TEST_PK')),
    'stripe_sk' => env('STRIPE_SECRET', env('STRIPE_TEST_SK')),
    'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
];
