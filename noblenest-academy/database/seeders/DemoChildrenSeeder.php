<?php

namespace Database\Seeders;

use App\Models\ChildProfile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoChildrenSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Password1!');

        // Create demo parent accounts and their children
        $families = [
            [
                'parent' => [
                    'name' => 'Amira Hassan',
                    'email' => 'amira@demo.test',
                    'role' => 'Parent',
                    'email_verified_at' => now(),
                    'password' => $password,
                    'country_code' => 'AE',
                ],
                'children' => [
                    [
                        'name' => 'Yusuf Hassan',
                        'nickname' => 'Yoyo',
                        'date_of_birth' => now()->subYears(4)->subMonths(3),
                        'gender' => 'male',
                        'preferred_language' => 'ar',
                    ],
                    [
                        'name' => 'Fatima Hassan',
                        'nickname' => 'Tima',
                        'date_of_birth' => now()->subYears(6)->subMonths(1),
                        'gender' => 'female',
                        'preferred_language' => 'ar',
                    ],
                ],
            ],
            [
                'parent' => [
                    'name' => 'Sophie Leclerc',
                    'email' => 'sophie@demo.test',
                    'role' => 'Parent',
                    'email_verified_at' => now(),
                    'password' => $password,
                    'country_code' => 'FR',
                ],
                'children' => [
                    [
                        'name' => 'Lucas Leclerc',
                        'nickname' => 'Lulu',
                        'date_of_birth' => now()->subYears(3)->subMonths(8),
                        'gender' => 'male',
                        'preferred_language' => 'fr',
                    ],
                ],
            ],
            [
                'parent' => [
                    'name' => 'Priya Patel',
                    'email' => 'priya@demo.test',
                    'role' => 'Parent',
                    'email_verified_at' => now(),
                    'password' => $password,
                    'country_code' => 'GB',
                ],
                'children' => [
                    [
                        'name' => 'Anika Patel',
                        'nickname' => 'Ani',
                        'date_of_birth' => now()->subYears(5)->subMonths(5),
                        'gender' => 'female',
                        'preferred_language' => 'en',
                    ],
                    [
                        'name' => 'Rajan Patel',
                        'date_of_birth' => now()->subYears(7)->subMonths(2),
                        'gender' => 'male',
                        'preferred_language' => 'en',
                    ],
                ],
            ],
            [
                'parent' => [
                    'name' => 'Irina Volkov',
                    'email' => 'irina@demo.test',
                    'role' => 'Parent',
                    'email_verified_at' => now(),
                    'password' => $password,
                    'country_code' => 'RU',
                ],
                'children' => [
                    [
                        'name' => 'Misha Volkov',
                        'date_of_birth' => now()->subYears(2)->subMonths(10),
                        'gender' => 'male',
                        'preferred_language' => 'ru',
                    ],
                ],
            ],
            [
                'parent' => [
                    'name' => 'Li Wei',
                    'email' => 'liwei@demo.test',
                    'role' => 'Parent',
                    'email_verified_at' => now(),
                    'password' => $password,
                    'country_code' => 'CN',
                ],
                'children' => [
                    [
                        'name' => 'Mei Wei',
                        'nickname' => 'Mei',
                        'date_of_birth' => now()->subYears(4)->subMonths(7),
                        'gender' => 'female',
                        'preferred_language' => 'zh',
                    ],
                    [
                        'name' => 'Jun Wei',
                        'date_of_birth' => now()->subYears(6)->subMonths(9),
                        'gender' => 'male',
                        'preferred_language' => 'zh',
                    ],
                ],
            ],
            [
                'parent' => [
                    'name' => 'Fatou Diallo',
                    'email' => 'fatou@demo.test',
                    'role' => 'Parent',
                    'email_verified_at' => now(),
                    'password' => $password,
                    'country_code' => 'SN',
                ],
                'children' => [
                    [
                        'name' => 'Aminata Diallo',
                        'nickname' => 'Ami',
                        'date_of_birth' => now()->subYears(5)->subMonths(3),
                        'gender' => 'female',
                        'preferred_language' => 'fr',
                    ],
                ],
            ],
            [
                'parent' => [
                    'name' => 'James Okonkwo',
                    'email' => 'james@demo.test',
                    'role' => 'Parent',
                    'email_verified_at' => now(),
                    'password' => $password,
                    'country_code' => 'NG',
                ],
                'children' => [
                    [
                        'name' => 'Chisom Okonkwo',
                        'nickname' => 'Chi',
                        'date_of_birth' => now()->subYears(3)->subMonths(5),
                        'gender' => 'female',
                        'preferred_language' => 'en',
                    ],
                    [
                        'name' => 'Emeka Okonkwo',
                        'date_of_birth' => now()->subYears(8)->subMonths(1),
                        'gender' => 'male',
                        'preferred_language' => 'en',
                    ],
                ],
            ],
        ];

        foreach ($families as $family) {
            $parent = User::firstOrCreate(
                ['email' => $family['parent']['email']],
                $family['parent']
            );

            foreach ($family['children'] as $childData) {
                ChildProfile::firstOrCreate(
                    ['parent_id' => $parent->id, 'name' => $childData['name']],
                    array_merge($childData, [
                        'parent_id' => $parent->id,
                        // Demo: pre-grant parental consent so child-facing routes
                        // (activities, dashboard, assessment) render out of the box.
                        'parental_consent_at' => now(),
                    ])
                );
            }

            // Demo: every demo parent gets an ACTIVE subscription so the
            // subscription.active gate doesn't paywall the activity feed.
            // Real customers go through the Stripe checkout flow; this seed
            // is gated to local/testing in DatabaseSeeder (production block
            // does NOT call DemoChildrenSeeder).
            Subscription::firstOrCreate(
                ['user_id' => $parent->id, 'provider' => 'demo'],
                [
                    'plan' => 'family',
                    'provider_id' => 'demo_'.$parent->id,
                    'amount' => 0,
                    'currency' => 'USD',
                    'active' => true,
                    'status' => Subscription::STATUS_ACTIVE,
                    'starts_at' => now(),
                    'ends_at' => now()->addYear(),
                ]
            );
        }
    }
}
