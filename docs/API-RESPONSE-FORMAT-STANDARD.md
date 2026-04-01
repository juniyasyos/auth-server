# IAM API Response Format Standard

Standar format response API untuk memastikan data **lengkap, jelas, dan consistent** di semua endpoint.

---

## Response Format Guidelines

### ✅ Standard Response Wrapper

Semua API responses harus mengikuti format ini:

```json
{
  "source": "iam-server",
  "sub": "1",
  "user_id": 1,
  "timestamp": "2026-04-01T20:33:29+00:00",
  "data": {}  // endpoint-specific data
}
```

### Fields Mandatory di Root Level

| Field | Type | Deskripsi | Contoh |
|-------|------|-----------|--------|
| `source` | string | Selalu "iam-server" | "iam-server" |
| `sub` | string | Subject ID dari authenticated user | "1" |
| `user_id` | integer | User ID numeric | 1 |
| `timestamp` | string | ISO 8601 timestamp saat response | "2026-04-01T20:33:29+00:00" |

---

## Data Model Standards

### Application Model Response

Ketika return aplikasi, ALWAYS include fields ini:

```json
{
  "id": 1,
  "app_key": "siimut",
  "name": "SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu",
  "description": "Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja",
  "enabled": true,
  "status": "active",
  "logo_url": null,
  "has_logo": false,
  "app_url": "http://127.0.0.1:8088",
  "redirect_uris": ["http://127.0.0.1:8088"],
  "callback_url": null,
  "backchannel_url": null,
  "urls": {
    "primary": "http://127.0.0.1:8088",
    "all_redirects": ["http://127.0.0.1:8088"],
    "callback": null,
    "backchannel": null
  },
  "created_at": "2026-04-01T10:00:00+00:00",
  "updated_at": "2026-04-01T15:30:00+00:00"
}
```

**Field Requirements:**

| Field | Required | Type | Notes |
|-------|----------|------|-------|
| `id` | ✅ | integer | Unique identifier |
| `app_key` | ✅ | string | Unique, lowercase, no spaces |
| `name` | ✅ | string | **LENGKAP, bukan singkatan** |
| `description` | ✅ | string | Meaningful description |
| `enabled` | ✅ | boolean | Database field |
| `status` | ✅ | string | "active" atau "inactive" |
| `logo_url` | ⚠️ | string/null | URL atau null |
| `has_logo` | ✅ | boolean | Derived dari logo_url |
| `app_url` | ✅ | string | Primary URL (redirect_uris[0]) |
| `redirect_uris` | ✅ | array | JSON array |
| `callback_url` | ⚠️ | string/null | OAuth callback |
| `backchannel_url` | ⚠️ | string/null | Backchannel logout |
| `urls` | ✅ | object | Structured URL object |
| `created_at` | ⚠️ | string | ISO 8601 timestamp |
| `updated_at` | ⚠️ | string | ISO 8601 timestamp |

### Role Model Response

Ketika return role, ALWAYS include fields ini:

```json
{
  "id": 1,
  "slug": "super_admin",
  "name": "Super Admin",
  "description": "Hak penuh seluruh sistem, dapat mengakses dan mengelola semua modul",
  "is_system": false,
  "is_active": true,
  "application_id": 1,
  "permissions_count": 5,
  "created_at": "2026-04-01T10:00:00+00:00"
}
```

**Field Requirements:**

| Field | Required | Type | Notes |
|-------|----------|------|-------|
| `id` | ✅ | integer | Unique identifier |
| `slug` | ✅ | string | For programming logic |
| `name` | ✅ | string | Human-readable |
| `description` | ✅ | string | **JELAS dan MEANINGFUL** |
| `is_system` | ✅ | boolean | System role? |
| `is_active` | ✅ | boolean | Active status |
| `application_id` | ✅ | integer | Which app |
| `permissions_count` | ⚠️ | integer | Number of perms |
| `created_at` | ⚠️ | string | ISO 8601 timestamp |

### User Model Response

Ketika return user data:

```json
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@example.com",
  "nip": "1234567890",
  "active": true,
  "email_verified_at": "2026-04-01T10:00:00+00:00",
  "created_at": "2026-04-01T10:00:00+00:00",
  "updated_at": "2026-04-01T15:30:00+00:00"
}
```

---

## Endpoint Response Specifications

### GET /api/users/applications

**Deskripsi:** Basic aplikasi list untuk app switcher

**Response Structure:**
```json
{
  "source": "iam-server",
  "sub": "1",
  "user_id": 1,
  "total_accessible_apps": 2,
  "applications": [
    {
      "id": 1,
      "app_key": "siimut",
      "name": "SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu",
      "description": "...",
      "enabled": true,
      "status": "active",
      "logo_url": null,
      "has_logo": false,
      "app_url": "http://127.0.0.1:8088",
      "redirect_uris": [...],
      "roles": [
        {
          "id": 1,
          "slug": "super_admin",
          "name": "Super Admin",
          "description": "Hak penuh seluruh sistem",
          "is_system": false
        }
      ],
      "roles_count": 1,
      "urls": {
        "primary": "...",
        "all_redirects": [...]
      }
    }
  ],
  "accessible_apps": ["siimut"],
  "timestamp": "2026-04-01T20:33:29+00:00"
}
```

**Implementation (Laravel Resource):**

```php
// app/Http/Resources/UserApplicationResource.php
class UserApplicationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'app_key' => $this->app_key,
            'name' => $this->name, // ✅ Full name, not shortened
            'description' => $this->description, // ✅ Always present
            'enabled' => $this->enabled,
            'status' => $this->enabled ? 'active' : 'inactive',
            'logo_url' => $this->logo_url,
            'has_logo' => !empty($this->logo_url),
            'app_url' => $this->getPrimaryUrl(),
            'redirect_uris' => $this->redirect_uris ?? [],
            'roles' => $this->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'slug' => $role->slug,
                    'name' => $role->name,
                    'description' => $role->description, // ✅ Always include
                    'is_system' => $role->is_system,
                ];
            }),
            'roles_count' => $this->roles->count(),
            'urls' => [
                'primary' => $this->getPrimaryUrl(),
                'all_redirects' => $this->redirect_uris ?? [],
            ],
        ];
    }

    private function getPrimaryUrl(): ?string
    {
        if (is_array($this->redirect_uris) && !empty($this->redirect_uris)) {
            return $this->redirect_uris[0];
        }
        return null;
    }
}
```

---

### GET /api/users/applications/detail

**Deskripsi:** Complete aplikasi data dengan metadata lengkap

**Response Structure:**
```json
{
  "source": "iam-server",
  "sub": "1",
  "user_id": 1,
  "total_apps": 1,
  "applications": [
    {
      "id": 1,
      "app_key": "siimut",
      "name": "SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu",
      "description": "...",
      "status": "active",
      "metadata": {
        "logo": {
          "url": null,
          "available": false
        },
        "urls": {
          "primary": "http://127.0.0.1:8088",
          "all_redirects": ["http://127.0.0.1:8088"],
          "callback": null,
          "backchannel": null
        },
        "created_at": "2026-04-01T10:00:00+00:00",
        "updated_at": "2026-04-01T15:30:00+00:00"
      },
      "roles": [...],
      "roles_count": 1,
      "access_profiles_using_this_app": [...]
    }
  ],
  "user_profiles": [...],
  "timestamp": "2026-04-01T20:33:29+00:00"
}
```

---

## Best Practices

### 1. Field Name Consistency

✅ **GOOD:**
```json
{
  "id": 1,
  "app_key": "siimut",
  "name": "SIIMUT - ...",
  "description": "...",
  "status": "active"
}
```

❌ **BAD:**
```json
{
  "id": 1,
  "app_id": 1,
  "application_key": "siimut",
  "title": "SIIMUT",
  "summary": "..."
}
```

### 2. Always Include Descriptions

✅ **GOOD:**
```json
{
  "role": {
    "name": "Super Admin",
    "description": "Hak penuh seluruh sistem, dapat mengakses dan mengelola semua modul tanpa batasan"
  }
}
```

❌ **BAD:**
```json
{
  "role": {
    "name": "Super Admin"
  }
}
```

### 3. Use Consistent UTC Timestamps

✅ **GOOD:**
```json
{
  "created_at": "2026-04-01T10:00:00+00:00",
  "timestamp": "2026-04-01T20:33:29+00:00"
}
```

❌ **BAD:**
```json
{
  "created": "2026-04-01 10:00:00",
  "timestamp": 1743638009
}
```

### 4. Avoid Null Fields (Use Empty String atau Array)

⚠️ **CONSIDER:**
```json
{
  "description": "...",  // Always string, never null
  "redirect_uris": [],   // Always array, never null
  "logo_url": null       // OK untuk optional URL fields
}
```

### 5. Full Names, Not Abbreviations

✅ **GOOD:**
```json
{
  "name": "SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu"
}
```

❌ **BAD:**
```json
{
  "name": "Siimut"
}
```

---

## Validation Rules

### untuk Application Model

```php
// app/Domain/Iam/Rules/ApplicationValidationRules.php
public static function rules(): array
{
    return [
        'app_key' => 'required|string|max:100|unique:iam_applications|lowercase|regex:/^[a-z0-9_-]+$/',
        'name' => 'required|string|max:255|min:10', // Min 10 chars to ensure not abbreviated
        'description' => 'required|string|max:1000|min:20', // Min 20 chars
        'enabled' => 'required|boolean',
        'logo_url' => 'nullable|url|max:500',
        'redirect_uris' => 'required|array|min:1',
        'redirect_uris.*' => 'url|max:500',
        'callback_url' => 'nullable|url|max:500',
        'backchannel_url' => 'nullable|url|max:500',
    ];
}
```

### untuk Role Model

```php
// app/Domain/Iam/Rules/RoleValidationRules.php
public static function rules(): array
{
    return [
        'application_id' => 'required|integer|exists:iam_applications,id',
        'slug' => 'required|string|max:100|lowercase|regex:/^[a-z0-9_-]+$/',
        'name' => 'required|string|max:100',
        'description' => 'required|string|max:500|min:10', // Min 10 chars
        'is_system' => 'required|boolean',
        'is_active' => 'required|boolean',
    ];
}
```

---

## Testing

### Feature Test Template

```php
// tests/Feature/Api/UserApplicationsCompletnessTest.php

test('applications endpoint returns complete data structure', function () {
    $user = User::factory()->create();
    $app = Application::factory()->create([
        'name' => 'SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu',
        'description' => 'Aplikasi manajemen indikator kinerja...',
    ]);
    $role = ApplicationRole::factory()->create([
        'application_id' => $app->id,
        'description' => 'Hak penuh seluruh sistem',
    ]);
    $user->applicationRoles()->attach($role);

    $response = $this->actingAs($user, 'api')
        ->getJson('/api/users/applications');

    // Check source
    $response->assertJsonPath('source', 'iam-server');
    
    // Check application completeness
    $response->assertJsonPath('applications.0.id', $app->id);
    $response->assertJsonPath('applications.0.app_key', $app->app_key);
    $response->assertJsonPath('applications.0.name', 'SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu');
    $response->assertJsonPath('applications.0.description', 'Aplikasi manajemen indikator kinerja...');
    $response->assertJsonPath('applications.0.enabled', true);
    $response->assertJsonPath('applications.0.status', 'active');
    
    // Check role completeness
    $response->assertJsonPath('applications.0.roles.0.slug', $role->slug);
    $response->assertJsonPath('applications.0.roles.0.name', $role->name);
    $response->assertJsonPath('applications.0.roles.0.description', 'Hak penuh seluruh sistem');
});
```

---

## Migration Checklist

Sebelum go-live, pastikan:

- [ ] Semua aplikasi punya `name` lengkap (min 20 chars)
- [ ] Semua aplikasi punya `description` (min 30 chars)
- [ ] Semua roles punya `description` (min 10 chars)
- [ ] Semua roles punya `slug` yang valid
- [ ] Test semua endpoints return complete data
- [ ] Validate JSON response structure dengan schema validator
- [ ] Check null/empty fields tidak ada (kecuali optional URL fields)
- [ ] Run feature tests untuk data completeness
- [ ] Document API responses di README

---

## References

- [API User Applications Endpoints](./API-USER-APPLICATIONS-ENDPOINTS.md)
- [Data Completeness Checklist](./DATA-COMPLETENESS-CHECKLIST.md)
