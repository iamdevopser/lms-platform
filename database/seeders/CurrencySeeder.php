<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.000000,
                'is_active' => true,
                'is_default' => true,
                'decimal_places' => 2,
                'position' => 'left'
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.920000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'position' => 'left'
            ],
            [
                'code' => 'TRY',
                'name' => 'Turkish Lira',
                'symbol' => '₺',
                'exchange_rate' => 31.500000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'position' => 'right'
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'exchange_rate' => 0.790000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'position' => 'left'
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'exchange_rate' => 150.000000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 0,
                'position' => 'left'
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'C$',
                'exchange_rate' => 1.350000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'position' => 'left'
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'exchange_rate' => 1.520000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'position' => 'left'
            ],
            [
                'code' => 'CHF',
                'name' => 'Swiss Franc',
                'symbol' => 'CHF',
                'exchange_rate' => 0.880000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'position' => 'left'
            ],
            [
                'code' => 'CNY',
                'name' => 'Chinese Yuan',
                'symbol' => '¥',
                'exchange_rate' => 7.200000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'position' => 'left'
            ],
            [
                'code' => 'INR',
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'exchange_rate' => 83.000000,
                'is_active' => true,
                'is_default' => false,
                'decimal_places' => 2,
                'position' => 'left'
            ]
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
} 