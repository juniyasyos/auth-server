<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Setting up Passport Clients...');

        // Create Personal Access Client if not exists
        if (Client::where('personal_access_client', true)->doesntExist()) {
            Client::create([
                'name' => 'Personal Access Client',
                'secret' => \Illuminate\Support\Str::random(40),
                'redirect' => 'http://localhost',
                'personal_access_client' => true,
                'password_client' => false,
                'revoked' => false,
            ]);
            $this->command->info('✅ Personal Access Client created.');
        } else {
            $this->command->info('ℹ️  Personal Access Client already exists.');
        }

        // Create Password Grant Client if not exists
        if (Client::where('password_client', true)->doesntExist()) {
            Client::create([
                'name' => 'Password Grant Client',
                'secret' => \Illuminate\Support\Str::random(40),
                'redirect' => 'http://localhost',
                'personal_access_client' => false,
                'password_client' => true,
                'revoked' => false,
            ]);
            $this->command->info('✅ Password Grant Client created.');
        } else {
            $this->command->info('ℹ️  Password Grant Client already exists.');
        }
    }
}
