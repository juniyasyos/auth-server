<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed basic users with unit information
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'password' => 'password',
                'active' => true,
            ],
            [
                'name' => 'Dr. John Doe',
                'email' => 'doctor@gmail.com',
                'password' => 'password',
                'active' => true,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'nurse@gmail.com',
                'password' => 'password',
                'active' => true,
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@gmail.com',
                'password' => 'password',
                'active' => true,
            ],
            [
                'name' => 'Pharmacist User',
                'email' => 'pharmacist@gmail.com',
                'password' => 'password',
                'active' => true,
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@gmail.com',
                'password' => 'password',
                'active' => true,
            ],
        ];

        foreach ($users as $data) {
            User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'active' => $data['active'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Users seeded successfully!');
    }
}
