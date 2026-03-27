<?php

namespace Database\Seeders;

use App\Domain\Iam\Models\AccessProfile;
use App\Domain\Iam\Models\Application;
use App\Domain\Iam\Models\ApplicationRole;
use Illuminate\Database\Seeder;

class AccessProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar profil akses global yang umum di RS kamu
        $profiles = [
            [
                'slug'        => 'super_admin',
                'name'        => 'Super Admin',
                'description' => 'Memiliki hak akses penuh terhadap seluruh fitur dan konfigurasi sistem, termasuk manajemen pengguna, pengaturan, dan kontrol data secara menyeluruh.',
                'is_system'   => true,
            ],
            [
                'slug'        => 'tim_mutu',
                'name'        => 'Tim Mutu',
                'description' => 'Bertanggung jawab dalam pengelolaan, validasi, serta evaluasi indikator mutu rumah sakit untuk memastikan standar kualitas layanan terpenuhi.',
                'is_system'   => true,
            ],

            [
                'slug'        => 'validator_pic',
                'name'        => 'Unit Kerja: PIC Indikator',
                'description' => 'Berperan sebagai penanggung jawab indikator mutu pada unit kerja masing-masing, termasuk pemantauan, pelaporan, dan tindak lanjut capaian.',
                'is_system'   => false,
            ],
            [
                'slug'        => 'pengumpul_data',
                'name'        => 'Unit Kerja: Pengumpul Data',
                'description' => 'Bertugas melakukan pengumpulan dan input data operasional sesuai indikator yang telah ditetapkan untuk mendukung proses evaluasi mutu.',
                'is_system'   => false,
            ],
        ];

        foreach ($profiles as $profile) {
            AccessProfile::updateOrCreate(
                ['slug' => $profile['slug']],
                [
                    'name'        => $profile['name'],
                    'description' => $profile['description'],
                    'is_system'   => $profile['is_system'],
                    'is_active'   => true,
                ]
            );
        }

        // Mapping access profiles to application roles for clarity and maintainability.
        // super_admin => semua admin,
        // tim_mutu => tim_mutu di siimut,
        // pic_indikator => role pic_indikator di siimut,
        // pengumpul_data => role pengumpul_data di siimut + admin di apps lain jika diperlukan
        // $mappings = [
        //     'super_admin' => [
        //         'siimut' => ['super_admin'],
        //         'incident-report.app' => ['admin'],
        //     ],
        //     'tim_mutu' => [
        //         'siimut' => ['tim_mutu'],
        //     ],
        //     'pic_indikator' => [
        //         'siimut' => ['pic_indikator'],
        //     ],
        //     'pengumpul_data' => [
        //         'siimut' => ['pengumpul_data'],
        //         'other-app' => ['admin'], // contoh jika perlu
        //     ],
        //     'admin_app' => [
        //         'client-example' => ['admin'],
        //         'incident-report.app' => ['admin'],
        //     ],
        // ];

        // foreach ($mappings as $profileSlug => $apps) {
        //     $profile = AccessProfile::where('slug', $profileSlug)->first();

        //     if (! $profile) {
        //         $this->command->warn("⚠️  AccessProfile '{$profileSlug}' not found, skipping mapping.");

        //         continue;
        //     }

        //     $roleIds = [];

        //     foreach ($apps as $appKey => $roleSlugs) {
        //         $application = Application::where('app_key', $appKey)->first();

        //         if (! $application) {
        //             $this->command->warn("⚠️  Application '{$appKey}' not found for mapping, skipping.");

        //             continue;
        //         }

        //         foreach ($roleSlugs as $roleSlug) {
        //             $role = ApplicationRole::where('application_id', $application->id)
        //                 ->where('slug', $roleSlug)
        //                 ->first();

        //             if ($role) {
        //                 $roleIds[] = $role->id;
        //             } else {
        //                 $this->command->warn("⚠️  Role '{$roleSlug}' for app '{$appKey}' not found while mapping.");
        //             }
        //         }
        //     }

        //     if (! empty($roleIds)) {
        //         $profile->roles()->syncWithoutDetaching(array_unique($roleIds));
        //         $this->command->info("  ✅ Mapped " . count($roleIds) . " role(s) to profile {$profile->name}");
        //     }
        // }
    }
}
