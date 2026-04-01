# IAM Server Data Completeness Checklist

Checklist untuk memastikan IAM server mengirimkan data **lengkap dan jelas** ke client applications.

---

## Aplikasi Metadata (Database Check)

### ✅ Required Fields untuk Applications

```sql
-- Check semua aplikasi punya data lengkap
SELECT 
  id,
  app_key,
  name,
  description,
  enabled,
  logo_url,
  redirect_uris,
  callback_url,
  backchannel_url,
  created_at,
  updated_at
FROM iam_applications
WHERE enabled = true;
```

Pastikan untuk setiap aplikasi:

| Field | Required | Type | Max Length | Example |
|-------|----------|------|------------|---------|
| `id` | ✅ | integer | - | 1 |
| `app_key` | ✅ | string | 100 | "siimut" |
| `name` | ✅ | string | 255 | "SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu" |
| `description` | ⚠️ | text | 1000+ | "Aplikasi manajemen indikator kinerja mutu rumah sakit..." |
| `enabled` | ✅ | boolean | - | true |
| `logo_url` | ⚠️ | string/null | 500 | "https://cdn.example.com/logos/siimut.png" atau null |
| `redirect_uris` | ✅ | JSON array | - | ["http://127.0.0.1:8088"] |
| `callback_url` | ⚠️ | string/null | 500 | "http://127.0.0.1:8088/oauth/callback" |
| `backchannel_url` | ⚠️ | string/null | 500 | "http://127.0.0.1:8088/backchannel" |
| `created_at` | ✅ | timestamp | - | Auto |
| `updated_at` | ✅ | timestamp | - | Auto |

**⚠️ = Optional tapi recommended untuk lengkap**

### Current Data Check

```php
// Jalankan di Tinker untuk check current applications
\App\Domain\Iam\Models\Application::where('enabled', true)
  ->get()
  ->each(function($app) {
    echo "=== {$app->app_key} ===\n";
    echo "Name: {$app->name}\n";
    echo "Desc: " . (strlen($app->description ?? '') > 50 ? substr($app->description, 0, 50) . '...' : $app->description) . "\n";
    echo "Logo: " . ($app->logo_url ? 'SET' : 'NOT SET') . "\n";
    echo "URLs: " . json_encode($app->redirect_uris) . "\n\n";
  });
```

---

## Role Metadata (Database Check)

### ✅ Required Fields untuk Roles

```sql
-- Check semua roles punya data lengkap
SELECT 
  id,
  application_id,
  slug,
  name,
  description,
  is_system,
  created_at,
  updated_at
FROM iam_roles
WHERE is_active = true
ORDER BY application_id, name;
```

Pastikan untuk setiap role:

| Field | Required | Type | Max Length | Example |
|-------|----------|------|------------|---------|
| `id` | ✅ | integer | - | 1 |
| `application_id` | ✅ | integer | - | 1 |
| `slug` | ✅ | string | 100 | "super_admin" |
| `name` | ✅ | string | 100 | "Super Admin" |
| `description` | ✅ | text | 500+ | "Hak penuh seluruh sistem" |
| `is_system` | ✅ | boolean | - | false |
| `is_active` | ✅ | boolean | - | true |
| `created_at` | ✅ | timestamp | - | Auto |
| `updated_at` | ✅ | timestamp | - | Auto |

### Current Roles Check

```php
// Jalankan di Tinker
\App\Domain\Iam\Models\ApplicationRole::with('application')
  ->where('is_active', true)
  ->get()
  ->each(function($role) {
    echo "App: {$role->application->app_key} | Role: {$role->name}\n";
    echo "  Slug: {$role->slug}\n";
    echo "  Desc: " . (empty($role->description) ? 'EMPTY' : substr($role->description, 0, 60) . '...') . "\n";
    echo "  System: " . ($role->is_system ? 'YES' : 'NO') . "\n\n";
  });
```

---

## Data Completeness Fix Queries

### Fix 1: Update Application Names (jika masih short)

```sql
UPDATE iam_applications SET name = 'SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu'
WHERE app_key = 'siimut';

-- Add lebih banyak aplikasi dengan nama lengkap sesuai kebutuhan bisnis
```

### Fix 2: Add Descriptions untuk Applications

```sql
UPDATE iam_applications SET 
  description = 'Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja'
WHERE app_key = 'siimut';

-- Update aplikasi lain dengan deskripsi yang meaningful
```

### Fix 3: Add Descriptions untuk Roles

```sql
UPDATE iam_roles SET 
  description = 'Hak penuh seluruh sistem, dapat mengakses dan mengelola semua modul'
WHERE slug = 'super_admin' AND application_id = 1;

UPDATE iam_roles SET 
  description = 'Pengguna regular dengan akses ke fitur umum'
WHERE slug = 'user' AND application_id = 1;

-- Pastikan setiap role punya deskripsi yang jelas
```

### Fix 4: Set Primary Redirect URI

```sql
-- Pastikan minimal ada satu redirect URI yang valid
UPDATE iam_applications SET 
  redirect_uris = JSON_ARRAY('http://127.0.0.1:8088')
WHERE app_key = 'siimut' AND redirect_uris IS NULL;
```

---

## API Response Validation

### Test dengan Curl

```bash
# Get Access Token
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' \
  | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)

# Test Applications Endpoint
curl -s -X GET http://127.0.0.1:8000/api/users/applications \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq .

# Validate response structure
curl -s -X GET http://127.0.0.1:8000/api/users/applications \
  -H "Authorization: Bearer $TOKEN" \
  | jq '
  .applications[0] | {
    app_key,
    name_length: (.name | length),
    has_description: (.description != null and .description != ""),
    roles_count,
    roles: (.roles | map({name, description_present: (.description != null)}))
  }' | jq .
```

### Expected Response Structure Validation

```php
// File: tests/Feature/Api/UserApplicationsValidationTest.php

test('applications endpoint returns complete data', function () {
    $user = User::factory()->create();
    $app = Application::factory()->create([
        'name' => 'SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu',
        'description' => 'Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja',
    ]);
    
    $role = ApplicationRole::factory()->create([
        'application_id' => $app->id,
        'name' => 'Super Admin',
        'description' => 'Hak penuh seluruh sistem',
        'slug' => 'super_admin',
    ]);
    
    $user->applicationRoles()->attach($role);
    
    $response = $this->actingAs($user, 'api')
        ->getJson('/api/users/applications');
    
    // Validate response structure
    $response->assertJsonStructure([
        'source',
        'sub',
        'user_id',
        'total_accessible_apps',
        'applications' => [
            '*' => [
                'id',
                'app_key',
                'name',
                'description',
                'enabled',
                'logo_url',
                'app_url',
                'redirect_uris',
                'roles' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'is_system',
                        'description',
                    ]
                ],
                'roles_count',
                'status',
                'has_logo',
                'has_primary_url',
                'urls' => [
                    'primary',
                    'all_redirects',
                ]
            ]
        ],
        'accessible_apps',
        'timestamp',
    ]);
    
    // Validate data completeness
    $app = $response->json('applications.0');
    
    expect($app['name'])->toBeTruthy()
        ->and($app['description'])->toBeTruthy()
        ->and($app['roles'][0]['description'])->toBeTruthy()
        ->and($app['roles_count'])->toBe(1);
});
```

---

## Seeder untuk Data Lengkap

### Update ApplicationsSeeder

```php
// File: database/seeders/ApplicationsSeeder.php

use App\Domain\Iam\Models\Application;

class ApplicationsSeeder extends Seeder
{
    public function run()
    {
        // SIIMUT Application
        Application::firstOrCreate(
            ['app_key' => 'siimut'],
            [
                'name' => 'SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu',
                'description' => 'Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja dengan fitur monitoring real-time dan reporting komprehensif',
                'enabled' => true,
                'logo_url' => null, // Set jika sudah punya logo
                'redirect_uris' => ['http://127.0.0.1:8088', 'http://siimut.example.com'],
                'callback_url' => 'http://127.0.0.1:8088/oauth/callback',
                'backchannel_url' => 'http://127.0.0.1:8088/backchannel',
            ]
        );

        // Add more applications as needed...
    }
}
```

### Update RolesSeeder

```php
// File: database/seeders/ApplicationRolesSeeder.php

use App\Domain\Iam\Models\Application;
use App\Domain\Iam\Models\ApplicationRole;

class ApplicationRolesSeeder extends Seeder
{
    public function run()
    {
        $siimut = Application::where('app_key', 'siimut')->first();

        if ($siimut) {
            ApplicationRole::firstOrCreate(
                [
                    'application_id' => $siimut->id,
                    'slug' => 'super_admin',
                ],
                [
                    'name' => 'Super Admin',
                    'description' => 'Hak penuh seluruh sistem, dapat mengakses dan mengelola semua modul tanpa batasan',
                    'is_system' => false,
                    'is_active' => true,
                ]
            );

            ApplicationRole::firstOrCreate(
                [
                    'application_id' => $siimut->id,
                    'slug' => 'manager',
                ],
                [
                    'name' => 'Manager',
                    'description' => 'Akses managerial, dapat mengelola data dan membuat laporan untuk unit kerja yang dipimpin',
                    'is_system' => false,
                    'is_active' => true,
                ]
            );

            ApplicationRole::firstOrCreate(
                [
                    'application_id' => $siimut->id,
                    'slug' => 'user',
                ],
                [
                    'name' => 'User',
                    'description' => 'Akses pengguna regular, dapat melihat data dan membuat laporan sesuai hak akses',
                    'is_system' => false,
                    'is_active' => true,
                ]
            );
        }
    }
}
```

---

## Checklist Sebelum Deploy

- ✅ Semua aplikasi punya `name` yang lengkap (bukan hanya singkatan)
- ✅ Semua aplikasi punya `description` yang meaningful
- ✅ Semua roles punya `description` yang jelas
- ✅ Minimal satu `redirect_uri` per aplikasi
- ✅ `logo_url` set jika aplikasi punya logo (optional tapi recommended)
- ✅ Callback URLs set jika menggunakan OAuth
- ✅ Test `/api/users/applications` response dengan Postman/Curl
- ✅ Validate JSON response structure
- ✅ Check data completeness di database
- ✅ Run feature tests untuk endpoint validation

---

## References

- [API User Applications Endpoints Doc](./API-USER-APPLICATIONS-ENDPOINTS.md)
- [IAM Architecture Docs](./ARCHITECTURE-DIAGRAMS.md)
- [Database Schema](../database/migrations)
