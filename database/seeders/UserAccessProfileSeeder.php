<?php

namespace Database\Seeders;

use App\Domain\Iam\Models\AccessProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserAccessProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👤 Assigning Access Profiles to Users...');

        $assignments = $this->getAssignments();

        foreach ($assignments as $assignment) {
            $user = User::where('nip', $assignment['nip'])->first();

            if (! $user) {
                $this->command->warn("⚠️  User with NIP '{$assignment['nip']}' not found, skipping.");
                continue;
            }

            $profileIds = [];
            foreach ($assignment['profiles'] as $profileSlug) {
                $profile = AccessProfile::where('slug', $profileSlug)->first();

                if ($profile) {
                    $profileIds[] = $profile->id;
                } else {
                    $this->command->warn("⚠️  Access profile '{$profileSlug}' not found.");
                }
            }

            if (!empty($profileIds)) {
                $user->accessProfiles()->syncWithoutDetaching($profileIds);
                $this->command->info("  ✅ Assigned " . count($profileIds) . " profile(s) to {$user->name}");
            }
        }

        $this->command->newLine();
        $this->command->info('✅ User access profile assignments completed!');
    }

    /**
     * Get user access profile assignments.
     */
    private function getAssignments(): array
    {
        $users = User::all();
        $store = [];
        foreach ($users as $user) {
            $store[] = [
                'nip' => $user->nip,
                'profiles' => ['unit_kerja'],
            ];
        }
        return $store;
    }
}
