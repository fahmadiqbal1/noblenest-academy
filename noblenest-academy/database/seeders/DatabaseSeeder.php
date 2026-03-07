<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Default login credentials (all passwords: Password1!):
     *   Admin   — admin@noblenest.test
     *   Teacher — teacher@noblenest.test
     *   Parent  — parent@noblenest.test
     *   Student — student@noblenest.test
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password1!');

        $users = [
            [
                'name'               => 'Admin User',
                'email'              => 'admin@noblenest.test',
                'role'               => 'Admin',
                'email_verified_at'  => now(),
                'password'           => $defaultPassword,
            ],
            [
                'name'               => 'Teacher User',
                'email'              => 'teacher@noblenest.test',
                'role'               => 'Teacher',
                'email_verified_at'  => now(),
                'password'           => $defaultPassword,
            ],
            [
                'name'               => 'Parent User',
                'email'              => 'parent@noblenest.test',
                'role'               => 'Parent',
                'email_verified_at'  => now(),
                'password'           => $defaultPassword,
            ],
            [
                'name'               => 'Student User',
                'email'              => 'student@noblenest.test',
                'role'               => 'Student',
                'email_verified_at'  => now(),
                'password'           => $defaultPassword,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->call([
            BasicCourseSeeder::class,
        ]);
    }
}
