<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswordSeeder extends Seeder
{
    /**
     * Reset all user passwords to 'rschjaya1234' except for user with NIP '0000.00000'.
     */
    public function run(): void
    {
        $this->command->info('🔐 Resetting user passwords...');

        $defaultPassword = 'rschjaya1234';
        $excludedNip = '0000.00000';

        try {
            // Get all users except the one with NIP '0000.00000'
            $users = User::where('nip', '!=', $excludedNip)
                ->orWhereNull('nip')
                ->get();

            $updatedCount = 0;

            foreach ($users as $user) {
                // Skip if NIP is exactly '0000.00000'
                if ($user->nip === $excludedNip) {
                    $this->command->line("⏭️  Skipping user: {$user->email} (NIP: {$user->nip})");
                    continue;
                }

                $user->update([
                    'password' => Hash::make($defaultPassword),
                ]);

                $updatedCount++;
                $this->command->line("✓ Updated: {$user->email} (NIP: {$user->nip})");
            }

            $this->command->info("✅ Password reset completed!");
            $this->command->info("   Total updated: {$updatedCount}");
            $this->command->info("   Excluded NIP: {$excludedNip}");
        } catch (\Exception $e) {
            $this->command->error('❌ Error resetting passwords: ' . $e->getMessage());
            throw $e;
        }
    }
}
