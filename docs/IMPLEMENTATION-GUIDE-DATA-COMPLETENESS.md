# IAM Server - Data Completeness Implementation Guide

Quick implementation guide untuk IAM server agar mengirimkan data **lengkap dan jelas** ke client applications.

---

## Quick Start (5 Steps)

### Step 1: Check Current Data Status

```bash
# SSH ke server atau gunakan Tinker
php artisan tinker

# Cek aplikasi data
App\Domain\Iam\Models\Application::where('enabled', true)
  ->pluck('app_key', 'name');

# Cek roles
App\Domain\Iam\Models\ApplicationRole::where('is_active', true)
  ->pluck('slug', 'name');
```

### Step 2: Update Database dengan Data Lengkap

```sql
-- 1. Update aplikasi dengan nama & deskripsi lengkap
UPDATE iam_applications SET 
  name = 'SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu',
  description = 'Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja dengan fitur monitoring real-time dan reporting komprehensif'
WHERE app_key = 'siimut';

-- 2. Pastikan redirect_uris ada
UPDATE iam_applications SET 
  redirect_uris = JSON_ARRAY('http://127.0.0.1:8088')
WHERE app_key = 'siimut' AND (redirect_uris IS NULL OR JSON_LENGTH(redirect_uris) = 0);

-- 3. Update roles dengan deskripsi
UPDATE iam_roles SET 
  description = 'Hak penuh seluruh sistem, dapat mengakses dan mengelola semua modul tanpa batasan'
WHERE slug = 'super_admin' AND application_id = (SELECT id FROM iam_applications WHERE app_key = 'siimut');
```

### Step 3: Test API Response

```bash
# Get token
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' \
  | jq -r '.access_token')

# Test aplikasi endpoint
curl -s http://localhost:8000/api/users/applications \
  -H "Authorization: Bearer $TOKEN" | jq '.'

# Validate specific fields
curl -s http://localhost:8000/api/users/applications \
  -H "Authorization: Bearer $TOKEN" | jq '.applications[0] | { name, description, roles }'
```

### Step 4: Verify Response Completeness

Response harus memiliki structure seperti ini:

```json
{
  "source": "iam-server",
  "sub": "1",
  "user_id": 1,
  "total_accessible_apps": 1,
  "applications": [
    {
      "id": 1,
      "app_key": "siimut",
      "name": "SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu",
      "description": "Aplikasi manajemen indikator kinerja mutu rumah sakit...",
      "enabled": true,
      "status": "active",
      "logo_url": null,
      "has_logo": false,
      "app_url": "http://127.0.0.1:8088",
      "redirect_uris": ["http://127.0.0.1:8088"],
      "roles": [
        {
          "id": 1,
          "slug": "super_admin",
          "name": "Super Admin",
          "description": "Hak penuh seluruh sistem...",
          "is_system": false
        }
      ],
      "roles_count": 1,
      "urls": {
        "primary": "http://127.0.0.1:8088",
        "all_redirects": ["http://127.0.0.1:8088"]
      }
    }
  ],
  "accessible_apps": ["siimut"],
  "timestamp": "2026-04-01T20:33:29+00:00"
}
```

### Step 5: Document untuk Client

Bagikan file dokumentasi ini ke client:
- 📄 `API-USER-APPLICATIONS-ENDPOINTS.md`
- 📄 `API-RESPONSE-FORMAT-STANDARD.md`

---

## What Changed? (Summary)

### ✅ Data Lengkap yang Sekarang Dikirim

**Application Data:**
- ✅ `name` - **Nama lengkap**, bukan singkatan (contoh: "SIIMUT - Sistem Informasi...")
- ✅ `description` - Deskripsi meaningful tentang aplikasi
- ✅ `status` - Status aplikasi (active/inactive)
- ✅ `roles_count` - Jumlah roles
- ✅ `urls` - Structured URLs (primary + all_redirects)

**Role Data:**
- ✅ `description` - **Penjelasan role**, contoh: "Hak penuh seluruh sistem"
- ✅ `slug` - Role identifier untuk programming logic
- ✅ `is_system` - Apakah role adalah system role

**Response Header:**
- ✅ `source` - Indicator bahwa data dari IAM server
- ✅ `sub` - Subject ID
- ✅ `user_id` - User ID numeric
- ✅ `timestamp` - ISO 8601 timestamp
- ✅ `accessible_apps` - Array of app keys yang accessible

### 🎯 Benefit untuk Client

| Sebelum | Sesudah |
|---------|---------|
| Hanya "Siimut" | "SIIMUT - Sistem Informasi..." |
| Tidak ada deskripsi app | Ada deskripsi yang jelas |
| Hanya nama role | Nama + deskripsi role |
| Tidak terstruktur | Konsisten & terstruktur |

---

## Field-by-Field Guide

### Application Name

**Apa yang dikirim:**
```json
{
  "name": "SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu"
}
```

**Rules:**
- ✅ Lengkap, bukan singkatan
- ✅ Jelas apa fungsinya
- ✅ Min 20 characters
- ✅ Format: "ACRONYM - Full Description"

**Database Check:**
```sql
SELECT app_key, name, LENGTH(name) as len FROM iam_applications WHERE enabled = true;
```

### Application Description

**Apa yang dikirim:**
```json
{
  "description": "Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja dengan fitur monitoring real-time dan reporting komprehensif"
}
```

**Rules:**
- ✅ Deskriptif tentang fungsi aplikasi
- ✅ Min 30 characters
- ✅ Jelas untuk end-user

**Database Check:**
```sql
SELECT app_key, description FROM iam_applications 
WHERE enabled = true AND (description IS NULL OR LENGTH(description) < 30);
```

### Role Description

**Apa yang dikirim:**
```json
{
  "role": {
    "name": "Super Admin",
    "description": "Hak penuh seluruh sistem, dapat mengakses dan mengelola semua modul tanpa batasan"
  }
}
```

**Rules:**
- ✅ Jelas apa yg bisa dilakukan dengan role ini
- ✅ Min 10 characters
- ✅ Concrete examples jika perlu

**Database Check:**
```sql
SELECT slug, name, description FROM iam_roles 
WHERE is_active = true AND (description IS NULL OR LENGTH(description) < 10);
```

---

## Database Fixes (If Needed)

### Fix 1: Update Application Names

```sql
-- Template query untuk update semua aplikasi
UPDATE iam_applications SET 
  name = CASE app_key
    WHEN 'siimut' THEN 'SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu'
    WHEN 'another-app' THEN 'ANOTHER APP - Full Description'
    ELSE name
  END,
  description = CASE app_key
    WHEN 'siimut' THEN 'Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja dengan fitur monitoring real-time dan reporting komprehensif'
    WHEN 'another-app' THEN 'Description here...'
    ELSE description
  END
WHERE enabled = true;
```

### Fix 2: Add Role Descriptions

```sql
-- Template untuk role descriptions
UPDATE iam_roles SET 
  description = CASE slug
    WHEN 'super_admin' THEN 'Hak penuh seluruh sistem, dapat mengakses dan mengelola semua modul tanpa batasan'
    WHEN 'manager' THEN 'Akses managerial, dapat mengelola data dan membuat laporan untuk unit kerja'
    WHEN 'user' THEN 'Akses pengguna regular, dapat melihat data sesuai hak akses'
    ELSE description
  END
WHERE is_active = true AND (description IS NULL OR description = '');
```

### Fix 3: Ensure Redirect URIs

```sql
-- Set default redirect URIs jika kosong
UPDATE iam_applications SET 
  redirect_uris = JSON_ARRAY(
    COALESCE(
      JSON_EXTRACT(redirect_uris, '$[0]'),
      CONCAT('http://', app_key, '.example.com'),
      'http://localhost:8088'
    )
  )
WHERE enabled = true AND (redirect_uris IS NULL OR JSON_LENGTH(redirect_uris) = 0);
```

---

## Validation Script

Jalankan script ini untuk validate data completeness:

```php
// artisan tinker

// 1. Check aplikasi completeness
echo "=== APPLICATION COMPLETENESS ===\n";
App\Domain\Iam\Models\Application::where('enabled', true)
  ->each(function($app) {
    $score = 0;
    $checks = [];
    
    // Check name length
    if (strlen($app->name) >= 20) {
      $score++;
      $checks[] = "✅ Name length OK";
    } else {
      $checks[] = "❌ Name too short: {$app->name}";
    }
    
    // Check description
    if (!empty($app->description) && strlen($app->description) >= 30) {
      $score++;
      $checks[] = "✅ Description OK";
    } else {
      $checks[] = "❌ Description missing or too short";
    }
    
    // Check redirect_uris
    if (!empty($app->redirect_uris) && is_array($app->redirect_uris)) {
      $score++;
      $checks[] = "✅ Redirect URIs OK";
    } else {
      $checks[] = "❌ Redirect URIs missing";
    }
    
    echo "\n{$app->app_key}: $score/3\n";
    foreach ($checks as $check) {
      echo "  $check\n";
    }
  });

// 2. Check roles completeness
echo "\n\n=== ROLES COMPLETENESS ===\n";
App\Domain\Iam\Models\ApplicationRole::where('is_active', true)
  ->with('application')
  ->each(function($role) {
    $hasDesc = !empty($role->description) && strlen($role->description) >= 10;
    $status = $hasDesc ? "✅" : "❌";
    echo "$status {$role->application->app_key}/{$role->slug}: ";
    echo $hasDesc ? "OK" : "Missing/Short description";
    echo "\n";
  });

exit();
```

---

## Testing Endpoints

### Test 1: Basic Applications Endpoint

```bash
# Get token
TOKEN="your-token-here"

# Test endpoint
curl -X GET http://localhost:8000/api/users/applications \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  | jq '.applications[0]'

# Expected fields present
curl -X GET http://localhost:8000/api/users/applications \
  -H "Authorization: Bearer $TOKEN" \
  | jq '.applications[0] | keys'
```

### Test 2: Validate Response Structure

```bash
# Validate application object has all required fields
curl -X GET http://localhost:8000/api/users/applications \
  -H "Authorization: Bearer $TOKEN" \
  | jq 'has("source") and has("sub") and has("user_id") and has("applications")'

# Validate application data
curl -X GET http://localhost:8000/api/users/applications \
  -H "Authorization: Bearer $TOKEN" \
  | jq '.applications[0] | has("name") and has("description") and has("roles")'
```

---

## Checklist Sebelum Production

- [ ] Semua enabled aplikasi punya nama lengkap (min 20 chars)
- [ ] Semua aplikasi punya description yang meaningful (min 30 chars)
- [ ] Semua active roles punya description (min 10 chars)
- [ ] Test `/api/users/applications` mengembalikan data lengkap
- [ ] Test role descriptions muncul di response
- [ ] Validate JSON structure dengan jq atau JSON schema validator
- [ ] Run validation script & semua pass
- [ ] Database backup sebelum menjalankan fix queries
- [ ] Test dengan real user login (bukan seed data)
- [ ] Share dokumentasi ke client team

---

## Documentation Files to Share

📁 **Untuk Client Development Team:**
1. `docs/API-USER-APPLICATIONS-ENDPOINTS.md` - API reference
2. `docs/API-RESPONSE-FORMAT-STANDARD.md` - Response format & best practices
3. `docs/DATA-COMPLETENESS-CHECKLIST.md` - Database requirements

📁 **Untuk Ops/DevOps:**
1. `docs/DATA-COMPLETENESS-CHECKLIST.md` - Maintenance checklist

---

## FAQ

**Q: Bagaimana jika aplikasi sudah di production dengan data lama?**
A: Jalankan fix queries di backup database dulu, test, then apply ke production dengan maintenance window.

**Q: Apakah ini breaking change untuk client?**
A: Tidak! Ini additive change. Response structure tetap sama, hanya field nilainya lebih lengkap.

**Q: Client sudah implement tanpa field-field baru?**
A: Mereka bisa update UI mereka untuk menampilkan deskripsi dan data baru yang sekarang available.

---

## Support

Jika ada pertanyaan tentang implementation, lihat:
- Documentation di `/docs/` folder
- Feature test examples di `/tests/Feature/`
- Cek validation rules di `/app/Domain/Iam/Rules/`

## References

- [API User Applications Endpoints](./API-USER-APPLICATIONS-ENDPOINTS.md)
- [API Response Format Standard](./API-RESPONSE-FORMAT-STANDARD.md)
- [Data Completeness Checklist](./DATA-COMPLETENESS-CHECKLIST.md)
