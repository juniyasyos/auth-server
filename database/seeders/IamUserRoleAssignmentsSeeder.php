<?php

namespace Database\Seeders;

use App\Domain\Iam\Models\Application;
use App\Domain\Iam\Models\ApplicationRole;
use App\Domain\Iam\Models\UserApplicationRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class IamUserRoleAssignmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Seeding IAM User Role Assignments...');

        $assignments = $this->getAssignments();

        foreach ($assignments as $assignment) {
            $user = User::where('email', $assignment['email'])->first();

            if (! $user) {
                $this->command->warn("⚠️  User '{$assignment['email']}' not found, skipping.");

                continue;
            }

            foreach ($assignment['roles'] as $appKey => $roleSlugs) {
                $application = Application::where('app_key', $appKey)->first();

                if (! $application) {
                    $this->command->warn("⚠️  Application '{$appKey}' not found, skipping.");

                    continue;
                }

                foreach ($roleSlugs as $roleSlug) {
                    $role = ApplicationRole::where('application_id', $application->id)
                        ->where('slug', $roleSlug)
                        ->first();

                    if (! $role) {
                        $this->command->warn("⚠️  Role '{$roleSlug}' for app '{$appKey}' not found.");

                        continue;
                    }

                    UserApplicationRole::firstOrCreate([
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                    ]);
                }

                $this->command->info("  ✅ Assigned ".count($roleSlugs)." role(s) to {$user->name} for {$application->name}");
            }
        }

        $this->command->newLine();
        $this->command->info('✅ User role assignments completed!');
    }

    /**
     * Get user role assignments.
     */
    private function getAssignments(): array
    {
        return [
            [
                'email' => 'admin@gmail.com',
                'roles' => [
                    'client-example' => ['admin'],
                    'siimut' => ['admin', 'viewer'],
                    'tamasuma' => ['admin'],
                    'incident-report.app' => ['admin'],
                    'pharmacy.app' => ['admin'],
                ],
            ],
            [
                'email' => 'doctor@gmail.com',
                'roles' => [
                    'client-example' => ['viewer'],
                    'siimut' => ['doctor', 'viewer'],
                    'incident-report.app' => ['reporter'],
                ],
            ],
            [
                'email' => 'nurse@gmail.com',
                'roles' => [
                    'client-example' => ['viewer'],
                    'siimut' => ['nurse', 'viewer'],
                    'incident-report.app' => ['reporter'],
                ],
            ],
            [
                'email' => 'pharmacist@gmail.com',
                'roles' => [
                    'client-example' => ['viewer'],
                    'siimut' => ['viewer'],
                    'pharmacy.app' => ['pharmacist'],
                ],
            ],
            [
                'email' => 'manager@gmail.com',
                'roles' => [
                    'client-example' => ['viewer'],
                    'siimut' => ['viewer'],
                    'tamasuma' => ['manager'],
                    'incident-report.app' => ['officer', 'viewer'],
                ],
            ],
            [
                'email' => 'staff@gmail.com',
                'roles' => [
                    'client-example' => ['viewer'],
                    'siimut' => ['receptionist'],
                    'tamasuma' => ['staff'],
                    'incident-report.app' => ['reporter'],
                ],
            ],
        ];
    }
}
