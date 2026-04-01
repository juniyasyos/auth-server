# IAM User Applications API Endpoints

Dokumentasi lengkap endpoint untuk mengambil data aplikasi yang accessible oleh user.

## Overview

Ada 3 endpoint utama untuk mengambil informasi aplikasi:

| Endpoint | Method | Deskripsi | Use Case |
|----------|--------|-----------|----------|
| `/api/users/applications` | GET | Basic applications list dengan roles | App Switcher, Quick Navigation |
| `/api/users/applications/detail` | GET | Complete detailed applications | Admin Panels, Full Metadata |
| `/api/user` atau `/api/users/me` | GET | Complete user data + applications | General User Info |

---

## 1. Endpoint: GET `/api/users/applications`

**Basic Applications List** dengan metadata lengkap untuk app switcher.

### Headers Required
```http
Authorization: Bearer {access_token}
Accept: application/json
```

### Response Example

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
      "description": "Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja",
      "enabled": true,
      "logo_url": null,
      "app_url": "http://127.0.0.1:8088",
      "redirect_uris": [
        "http://127.0.0.1:8088"
      ],
      "roles": [
        {
          "id": 1,
          "slug": "super_admin",
          "name": "Super Admin",
          "is_system": false,
          "description": "Hak penuh seluruh sistem"
        }
      ],
      "roles_count": 1,
      "status": "active",
      "has_logo": false,
      "has_primary_url": true,
      "urls": {
        "primary": "http://127.0.0.1:8088",
        "all_redirects": [
          "http://127.0.0.1:8088"
        ]
      }
    }
  ],
  "accessible_apps": [
    "siimut"
  ],
  "timestamp": "2026-04-01T20:33:29+00:00"
}
```

### Response Fields

#### Root Level
| Field | Type | Deskripsi |
|-------|------|-----------|
| `source` | string | Sumber data (selalu "iam-server") |
| `sub` | string | Subject ID / User ID dari JWT |
| `user_id` | integer | User ID numeric |
| `total_accessible_apps` | integer | Total aplikasi yang accessible |
| `applications` | array | Daftar aplikasi (lihat struktur di bawah) |
| `accessible_apps` | array | Array of app_key yang accessible |
| `timestamp` | string | ISO 8601 timestamp |

#### Application Object
| Field | Type | Deskripsi | Contoh |
|-------|------|-----------|--------|
| `id` | integer | Application ID di IAM | 1 |
| `app_key` | string | Unique app identifier (lowercase, no spaces) | "siimut" |
| `name` | string | Nama lengkap aplikasi | "SIIMUT - Sistem Informasi..." |
| `description` | string/null | Deskripsi aplikasi | "Aplikasi manajemen indikator..." |
| `enabled` | boolean | Status enabled/disabled | true |
| `logo_url` | string/null | URL logo aplikasi | "https://..." atau null |
| `app_url` | string | Primary application URL (redirect_uris[0]) | "http://127.0.0.1:8088" |
| `redirect_uris` | array | Semua redirect URIs yang allowed | ["http://127.0.0.1:8088"] |
| `roles` | array | Roles user di aplikasi ini | (lihat struktur role) |
| `roles_count` | integer | Jumlah roles | 1 |
| `status` | string | Status aplikasi (active/inactive) | "active" |
| `has_logo` | boolean | Apakah ada logo | false |
| `has_primary_url` | boolean | Apakah ada primary URL | true |
| `urls` | object | URLs metadata | {primary, all_redirects} |

#### Role Object (dalam applications[].roles)
| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | integer | Role ID di IAM |
| `slug` | string | Role slug (untuk programming logic) |
| `name` | string | Human-readable role name |
| `is_system` | boolean | Apakah role adalah system role |
| `description` | string | Deskripsi role |

### Use Case

Gunakan endpoint ini untuk:
- ✅ **App Switcher** - Menampilkan daftar aplikasi yang user bisa akses
- ✅ **Quick Navigation** - Tombol untuk switch antar aplikasi
- ✅ **Dashboard** - Menampilkan aplikasi/role user
- ✅ **Mobile Menu** - Daftar aplikasi di mobile navigation

---

## 2. Endpoint: GET `/api/users/applications/detail`

**Detailed Applications** dengan metadata lengkap, timestamps, dan access profiles.

### Headers Required
```http
Authorization: Bearer {access_token}
Accept: application/json
```

### Response Example

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
      "description": "Aplikasi manajemen indikator kinerja mutu rumah sakit dan unit kerja",
      "status": "active",
      "metadata": {
        "logo": {
          "url": null,
          "available": false
        },
        "urls": {
          "primary": "http://127.0.0.1:8088",
          "all_redirects": [
            "http://127.0.0.1:8088"
          ],
          "callback": null,
          "backchannel": null
        },
        "created_at": "2026-04-01T10:00:00+00:00",
        "updated_at": "2026-04-01T15:30:00+00:00"
      },
      "roles": [
        {
          "id": 1,
          "slug": "super_admin",
          "name": "Super Admin",
          "is_system": false,
          "description": "Hak penuh seluruh sistem"
        }
      ],
      "roles_count": 1,
      "access_profiles_using_this_app": [
        {
          "id": 1,
          "name": "Super Admin",
          "slug": "super-admin"
        }
      ]
    }
  ],
  "user_profiles": [
    {
      "id": 1,
      "slug": "super-admin",
      "name": "Super Admin",
      "description": "Administrator utama sistem",
      "is_system": false,
      "roles_count": 5,
      "roles": [
        {
          "app_key": "siimut",
          "role_slug": "super_admin",
          "role_name": "Super Admin"
        }
      ]
    }
  ],
  "timestamp": "2026-04-01T20:33:29+00:00"
}
```

### Response Fields

#### Root Level
| Field | Type | Deskripsi |
|-------|------|-----------|
| `source` | string | Sumber data |
| `sub` | string | Subject ID / User ID |
| `user_id` | integer | User ID numeric |
| `total_apps` | integer | Total aplikasi |
| `applications` | array | Detail lengkap aplikasi |
| `user_profiles` | array | Access profiles user |
| `timestamp` | string | ISO 8601 timestamp |

#### Application Detail Object
| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | integer | Application ID |
| `app_key` | string | Unique app identifier |
| `name` | string | Nama lengkap aplikasi |
| `description` | string | Deskripsi aplikasi |
| `status` | string | Status (active/inactive) |
| `metadata` | object | Metadata lengkap (lihat struktur) |
| `roles` | array | Roles user di app ini |
| `roles_count` | integer | Jumlah roles |
| `access_profiles_using_this_app` | array | Access profiles yang memberikan akses |

#### Metadata Object
| Field | Type | Deskripsi |
|-------|------|-----------|
| `logo.url` | string/null | Logo URL |
| `logo.available` | boolean | Apakah logo available |
| `urls.primary` | string | Primary URL |
| `urls.all_redirects` | array | Semua redirect URIs |
| `urls.callback` | string/null | OAuth Callback URL |
| `urls.backchannel` | string/null | Backchannel Logout URL |
| `created_at` | string | ISO 8601 created timestamp |
| `updated_at` | string | ISO 8601 updated timestamp |

#### Access Profile Object
| Field | Type | Deskripsi |
|-------|------|-----------|
| `id` | integer | Profile ID |
| `name` | string | Profile name |
| `slug` | string | Profile slug |

### Use Case

Gunakan endpoint ini untuk:
- ✅ **Admin Panel** - Management aplikasi & access profiles
- ✅ **Detailed Audit** - Melihat siapa dapat akses via profile apa
- ✅ **Application Info Modal** - Menampilkan semua detail aplikasi
- ✅ **Timestamps** - Melihat kapan app dibuat/diupdate

---

## 3. Endpoint: GET `/api/users/me` atau `/api/user`

**Comprehensive User Data** dengan full profile + applications + roles.

### Headers Required
```http
Authorization: Bearer {access_token}
Accept: application/json
```

### Response Structure

```json
{
  "sub": "1",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "nip": "1234567890",
    "active": true,
    "email_verified_at": "2026-04-01T10:00:00+00:00",
    "created_at": "2026-04-01T10:00:00+00:00",
    "updated_at": "2026-04-01T10:00:00+00:00",
    "applications": [
      {
        "id": 1,
        "app_key": "siimut",
        "name": "SIIMUT...",
        "description": "...",
        "enabled": true,
        "logo_url": null,
        "app_url": "http://127.0.0.1:8088",
        "redirect_uris": ["http://127.0.0.1:8088"],
        "roles": [
          {
            "id": 1,
            "slug": "super_admin",
            "name": "Super Admin",
            "is_system": false,
            "description": "Hak penuh seluruh sistem"
          }
        ]
      }
    ],
    "accessible_apps": ["siimut"],
    "access_profiles": [
      {
        "id": 1,
        "slug": "super-admin",
        "name": "Super Admin",
        "description": "Administrator utama sistem",
        "is_system": false,
        "roles_count": 5,
        "roles": [
          {
            "app_key": "siimut",
            "role_slug": "super_admin",
            "role_name": "Super Admin"
          }
        ]
      }
    ],
    "direct_roles": [
      {
        "app_key": "siimut",
        "role_id": 1,
        "role_slug": "super_admin",
        "role_name": "Super Admin",
        "is_system": false
      }
    ]
  },
  "timestamp": "2026-04-01T20:33:29+00:00"
}
```

### Use Case

Gunakan endpoint ini untuk:
- ✅ **Initial Login** - Fetch semua data user sekaligus
- ✅ **Profile Page** - Menampilkan info user lengkap
- ✅ **Debug/Testing** - Lihat semua data termasuk direct roles & profiles

---

## Rekomendasi Client

### Untuk App Switcher (siimut)
```php
// ✅ GUNAKAN ENDPOINT INI
GET /api/users/applications

// Response langsung provide:
// - Nama aplikasi lengkap
// - Deskripsi
// - Logo URL (jika ada)
// - Roles dengan description
// - Status indicator
```

### Untuk mendapatkan data lengkap
```php
// Jika butuh metadata lengkap (timestamps, profiles, dll):
GET /api/users/applications/detail

// Jika butuh info user juga:
GET /api/users/me
```

---

## Troubleshooting

### Q: Response hanya menampilkan "Siimut" bukan nama lengkap?
**A:** Pastikan di database aplikasi field `name` sudah diupdate dengan nama lengkap:
```sql
UPDATE iam_applications 
SET name = 'SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu'
WHERE app_key = 'siimut';
```

### Q: Role description tidak ditampilkan?
**A:** Periksa di database bahwa role sudah punya deskripsi:
```sql
UPDATE iam_roles 
SET description = 'Hak penuh seluruh sistem'
WHERE slug = 'super_admin' AND application_id = 1;
```

### Q: Logo tidak tampil meski ada URL?
**A:** 
1. Ensure logo_url correct di database
2. Ensure image accessible dari client IP
3. Check CORS settings jika hosted di domain berbeda

---

## Response Headers

```http
HTTP/1.1 200 OK
Content-Type: application/json
Cache-Control: no-cache, private
```

---

## Status Codes

| Status | Meaning |
|--------|---------|
| 200 | Success |
| 401 | Unauthorized (invalid token) |
| 403 | Forbidden (user doesn't have access) |
| 500 | Server error |

---

## Notes

- Semua timestamp adalah ISO 8601 format
- `app_url` adalah primary URL dari `redirect_uris[0]`
- `status` adalah `enabled` field dari aplikasi (true = active, false = inactive)
- `accessible_apps` adalah array of app_key yang user dapat akses
- Untuk single app filtering, gunakan `?app=app_key` parameter di `/api/users/me`
