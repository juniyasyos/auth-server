<?php

namespace Database\Seeders;

use App\Domain\Iam\Models\Application;
use App\Domain\Iam\Models\ApplicationRole;
use Illuminate\Database\Seeder;

class IamRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎭 Seeding IAM Roles per Application...');

        $rolesData = $this->getRolesData();

        foreach ($rolesData as $appKey => $roles) {
            $application = Application::where('app_key', $appKey)->first();

            if (! $application) {
                $this->command->warn("⚠️  Application '{$appKey}' not found, skipping roles.");

                continue;
            }

            $this->command->info("  Creating roles for: {$application->name}");

            foreach ($roles as $roleData) {
                ApplicationRole::firstOrCreate(
                    [
                        'application_id' => $application->id,
                        'slug' => $roleData['slug'],
                    ],
                    [
                        'name' => $roleData['name'],
                        'description' => $roleData['description'],
                        'is_system' => $roleData['is_system'] ?? false,
                    ]
                );
            }

            $this->command->info("    ✅ Created ".count($roles).' roles');
        }

        $this->command->newLine();
        $this->command->info('✅ IAM Roles seeding completed!');
    }

    /**
     * Get roles data for each application.
     */
    private function getRolesData(): array
    {
        return [
            'client-example' => [
                [
                    'slug' => 'admin',
                    'name' => 'Administrator',
                    'description' => 'Full administrative access to client example app',
                    'is_system' => true,
                ],
                [
                    'slug' => 'viewer',
                    'name' => 'Viewer',
                    'description' => 'Read-only access to client example app',
                    'is_system' => false,
                ],
            ],

            'siimut' => [
                [
                    'slug' => 'admin',
                    'name' => 'Administrator SIIMUT',
                    'description' => 'Full administrative access to SIIMUT system',
                    'is_system' => true,
                ],
                [
                    'slug' => 'doctor',
                    'name' => 'Doctor',
                    'description' => 'Medical doctor with patient and prescription access',
                    'is_system' => false,
                ],
                [
                    'slug' => 'nurse',
                    'name' => 'Nurse',
                    'description' => 'Nursing staff with patient care access',
                    'is_system' => false,
                ],
                [
                    'slug' => 'receptionist',
                    'name' => 'Receptionist',
                    'description' => 'Front desk staff with patient registration access',
                    'is_system' => false,
                ],
                [
                    'slug' => 'viewer',
                    'name' => 'Viewer',
                    'description' => 'Read-only access to patient records',
                    'is_system' => false,
                ],
            ],

            'tamasuma' => [
                [
                    'slug' => 'admin',
                    'name' => 'Administrator Tamasuma',
                    'description' => 'Full administrative access to Tamasuma system',
                    'is_system' => true,
                ],
                [
                    'slug' => 'manager',
                    'name' => 'Manager',
                    'description' => 'Management level access',
                    'is_system' => false,
                ],
                [
                    'slug' => 'staff',
                    'name' => 'Staff',
                    'description' => 'General staff access',
                    'is_system' => false,
                ],
                [
                    'slug' => 'viewer',
                    'name' => 'Viewer',
                    'description' => 'Read-only access',
                    'is_system' => false,
                ],
            ],

            'incident-report.app' => [
                [
                    'slug' => 'admin',
                    'name' => 'Administrator Incident Report',
                    'description' => 'Full administrative access to incident reporting',
                    'is_system' => true,
                ],
                [
                    'slug' => 'officer',
                    'name' => 'Incident Officer',
                    'description' => 'Can create, edit, and manage incident reports',
                    'is_system' => false,
                ],
                [
                    'slug' => 'reporter',
                    'name' => 'Reporter',
                    'description' => 'Can create and submit incident reports',
                    'is_system' => false,
                ],
                [
                    'slug' => 'viewer',
                    'name' => 'Viewer',
                    'description' => 'Can view incident reports only',
                    'is_system' => false,
                ],
            ],

            'pharmacy.app' => [
                [
                    'slug' => 'admin',
                    'name' => 'Administrator Pharmacy',
                    'description' => 'Full administrative access to pharmacy system',
                    'is_system' => true,
                ],
                [
                    'slug' => 'pharmacist',
                    'name' => 'Pharmacist',
                    'description' => 'Licensed pharmacist with prescription access',
                    'is_system' => false,
                ],
                [
                    'slug' => 'assistant',
                    'name' => 'Pharmacy Assistant',
                    'description' => 'Pharmacy support staff',
                    'is_system' => false,
                ],
                [
                    'slug' => 'viewer',
                    'name' => 'Viewer',
                    'description' => 'Read-only access to pharmacy records',
                    'is_system' => false,
                ],
            ],
        ];
    }
}
