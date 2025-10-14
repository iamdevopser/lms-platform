<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'description' => 'Perfect for individual instructors just getting started',
                'price' => 9.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 7,
                'is_active' => true,
                'is_popular' => false,
                'features' => [
                    'Create up to 5 courses',
                    'Basic analytics',
                    'Email support',
                    'Standard course templates'
                ],
                'max_courses' => 5,
                'max_students' => 100,
                'priority_support' => false,
                'certificate_creation' => false,
                'advanced_analytics' => false
            ],
            [
                'name' => 'Professional',
                'description' => 'Ideal for growing instructors and small teams',
                'price' => 29.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'is_active' => true,
                'is_popular' => true,
                'features' => [
                    'Create unlimited courses',
                    'Advanced analytics',
                    'Priority support',
                    'Custom certificates',
                    'Advanced course templates',
                    'Student progress tracking'
                ],
                'max_courses' => null, // unlimited
                'max_students' => 1000,
                'priority_support' => true,
                'certificate_creation' => true,
                'advanced_analytics' => true
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large organizations and institutions',
                'price' => 99.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'is_active' => true,
                'is_popular' => false,
                'features' => [
                    'Everything in Professional',
                    'Unlimited students',
                    'White-label solution',
                    'API access',
                    'Custom integrations',
                    'Dedicated account manager',
                    'Advanced reporting'
                ],
                'max_courses' => null, // unlimited
                'max_students' => null, // unlimited
                'priority_support' => true,
                'certificate_creation' => true,
                'advanced_analytics' => true
            ],
            // Yearly plans with discount
            [
                'name' => 'Starter (Yearly)',
                'description' => 'Perfect for individual instructors just getting started - Save 20%',
                'price' => 95.90, // 9.99 * 12 * 0.8 (20% discount)
                'currency' => 'USD',
                'billing_cycle' => 'yearly',
                'trial_days' => 7,
                'is_active' => true,
                'is_popular' => false,
                'features' => [
                    'Create up to 5 courses',
                    'Basic analytics',
                    'Email support',
                    'Standard course templates',
                    'Save 20% with yearly billing'
                ],
                'max_courses' => 5,
                'max_students' => 100,
                'priority_support' => false,
                'certificate_creation' => false,
                'advanced_analytics' => false
            ],
            [
                'name' => 'Professional (Yearly)',
                'description' => 'Ideal for growing instructors and small teams - Save 20%',
                'price' => 287.90, // 29.99 * 12 * 0.8 (20% discount)
                'currency' => 'USD',
                'billing_cycle' => 'yearly',
                'trial_days' => 14,
                'is_active' => true,
                'is_popular' => true,
                'features' => [
                    'Create unlimited courses',
                    'Advanced analytics',
                    'Priority support',
                    'Custom certificates',
                    'Advanced course templates',
                    'Student progress tracking',
                    'Save 20% with yearly billing'
                ],
                'max_courses' => null, // unlimited
                'max_students' => 1000,
                'priority_support' => true,
                'certificate_creation' => true,
                'advanced_analytics' => true
            ],
            [
                'name' => 'Enterprise (Yearly)',
                'description' => 'For large organizations and institutions - Save 20%',
                'price' => 959.90, // 99.99 * 12 * 0.8 (20% discount)
                'currency' => 'USD',
                'billing_cycle' => 'yearly',
                'trial_days' => 30,
                'is_active' => true,
                'is_popular' => false,
                'features' => [
                    'Everything in Professional',
                    'Unlimited students',
                    'White-label solution',
                    'API access',
                    'Custom integrations',
                    'Dedicated account manager',
                    'Advanced reporting',
                    'Save 20% with yearly billing'
                ],
                'max_courses' => null, // unlimited
                'max_students' => null, // unlimited
                'priority_support' => true,
                'certificate_creation' => true,
                'advanced_analytics' => true
            ]
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                [
                    'name' => $plan['name'],
                    'billing_cycle' => $plan['billing_cycle']
                ],
                $plan
            );
        }
    }
} 