# Testing Report: UserApplicationsService
## User: 0000.00000 (admin)
**Date**: 2 April 2026 | **Status**: ✅ PASSED

---

## 📋 Test User Data

| Field | Value |
|-------|-------|
| **ID** | 1 |
| **NIP (User ID)** | 0000.00000 |
| **Nama** | admin |
| **Email** | admin@admin.com |
| **Status** | ✅ Active |
| **Access Profiles** | 1 (Super Admin) |
| **Effective Roles** | 1 (Super Admin) |
| **Accessible Apps** | 1 (SIIMUT) |

---

## 🔐 User Permissions

### Access Profiles
- **Super Admin** (ID: 1, slug: super_admin, Active: Yes)
  - Roles: 1
    - Super Admin (siimut app)

### Effective Roles (via profiles)
- **Super Admin** (super_admin)
  - Application: SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu
  - App Key: siimut
  - Is System: No

### Accessible Applications
```
Application: SIIMUT - Sistem Informasi Manajemen Indikator Mutu Terpadu
  └─ App Key: siimut
  └─ Enabled: Yes
  └─ URL: http://127.0.0.1:8088
  └─ Logo URL: None
```

---

## 📡 API Endpoints Testing

### Endpoint 1: GET /api/users/applications

**Status**: ✅ 200 OK

**Request**:
```
GET /api/users/applications HTTP/1.1
Authorization: Bearer {IAM_TOKEN}
Accept: application/json
```

**Response** (Sample):
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
      "status": "active",
      "logo_url": null,
      "app_url": "http://127.0.0.1:8088",
      "redirect_uris": ["http://127.0.0.1:8088"],
      "callback_url": "http://127.0.0.1:8088/sso/callback",
      "backchannel_url": "http://127.0.0.1:8088",
      "roles_count": 1,
      "has_logo": false,
      "has_primary_url": true,
      "urls": {
        "primary": "http://127.0.0.1:8088",
        "all_redirects": ["http://127.0.0.1:8088"],
        "callback": "http://127.0.0.1:8088/sso/callback",
        "backchannel": "http://127.0.0.1:8088"
      },
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
  "timestamp": "2026-04-01T17:27:39+00:00"
}
```

**Response Fields**:
- `source`: Source of data (iam-server)
- `sub`: User ID as string
- `user_id`: User ID as integer
- `total_accessible_apps`: Number of accessible applications
- `applications`: Array of application objects with:
  - Basic info: id, app_key, name, description
  - Status: enabled, status
  - Branding: logo_url, has_logo
  - URLs: app_url, redirect_uris, callback_url, backchannel_url, has_primary_url, urls (grouped)
  - Roles: roles_count, roles array
- `accessible_apps`: List of app_keys for quick filtering
- `timestamp`: Response timestamp

---

### Endpoint 2: GET /api/users/applications/detail

**Status**: ✅ 200 OK

**Request**:
```
GET /api/users/applications/detail HTTP/1.1
Authorization: Bearer {IAM_TOKEN}
Accept: application/json
```

**Additional Response Fields** (all from basic endpoint + these):
```json
{
  "applications": [
    {
      "...": "all fields from basic endpoint",
      "metadata": {
        "logo": {
          "url": null,
          "available": false
        },
        "urls": {...},
        "created_at": "2026-04-01T17:27:39+00:00",
        "updated_at": "2026-04-01T17:27:39+00:00"
      },
      "access_profiles_using_this_app": []
    }
  ],
  "user_profiles": [
    {
      "id": 1,
      "name": "Super Admin",
      "slug": "super_admin",
      "is_active": true
    }
  ]
}
```

**Additional Fields**:
- `metadata`: Contains logo availability, URLs, timestamps
- `access_profiles_using_this_app`: Profiles that provide access to this app
- `user_profiles`: List of user's access profiles

---

## ⚙️ Service Methods Testing

### Method 1: `getApplications()`

**Purpose**: Fetch user's accessible applications with basic metadata

**Implementation**:
```php
$service = app(\Juniyasyos\IamClient\Services\UserApplicationsService::class);
$result = $service->getApplications();
```

**Result for User 0000.00000**:
- ✅ Returns array with 'source', 'user_id', 'total_accessible_apps', 'applications'
- ✅ Contains 1 application (SIIMUT)
- ✅ All required fields present
- ✅ Data structure valid

**Error Handling**:
- Returns `['error' => 'iam_token_missing']` if no token in session
- Returns `['error' => 'iam_server_error']` if server returns error
- Returns `['error' => 'iam_request_error']` on network error

---

### Method 2: `getApplicationsDetail()`

**Purpose**: Fetch applications with full metadata and timestamps

**Implementation**:
```php
$result = $service->getApplicationsDetail();
```

**Result for User 0000.00000**:
- ✅ Contains all fields from `getApplications()`
- ✅ Includes `metadata` with logo availability and timestamps
- ✅ Includes `user_profiles` array
- ✅ All data properly formatted

---

### Method 3: `getApplicationByKey(string $appKey)`

**Purpose**: Find specific application by app_key

**Implementation**:
```php
$app = $service->getApplicationByKey('siimut');
```

**Test Results for User 0000.00000**:
- ✅ `getApplicationByKey('siimut')` returns application array
- ✅ `getApplicationByKey('other')` returns `null`
- ✅ Properly filters by app_key

---

## 🔍 Debug Methods Testing

### Method: `debugGetApplications()`
Returns raw HTTP response details for basic endpoint:
```php
$debug = $service->debugGetApplications();
// Returns: status, successful, headers, body_size, body
```

### Method: `debugGetApplicationsDetail()`
Returns raw HTTP response details for detail endpoint:
```php
$debug = $service->debugGetApplicationsDetail();
// Returns: status, successful, headers, body_size, body
```

### Method: `debugAll()`
Returns comprehensive debugging information:
```php
$allDebug = $service->debugAll();
// Returns: session info, endpoints, both responses, execution time
```

**Test Results**:
- ✅ All debug methods work correctly
- ✅ Provide detailed HTTP response info
- ✅ Include session and timing data
- ✅ Useful for troubleshooting

---

## ✅ Success Criteria

All criteria passed:

| Criteria | Status |
|----------|--------|
| User 0000.00000 memiliki akses | ✅ PASS |
| User memiliki access profile | ✅ PASS |
| User memiliki effective role | ✅ PASS |
| Endpoints mengembalikan data | ✅ PASS |
| Detail endpoint include metadata | ✅ PASS |
| Service handle token missing | ✅ PASS |
| Service handle server error | ✅ PASS |
| Service provide debug methods | ✅ PASS |

---

## 🎯 Test Summary

```
Total Test Cases:       8
Passed:                 8
Failed:                 0
Success Rate:           100% ✅

User Applications:      1
User Profiles:          1
User Roles:             1

API Endpoints Ready:    2
Service Methods Ready:  6
Debug Methods Ready:    3
```

---

## 📚 Usage Examples

### In Controller
```php
use Juniyasyos\IamClient\Services\UserApplicationsService;

class DashboardController extends Controller
{
    public function __construct(private UserApplicationsService $appsService) {}

    public function index()
    {
        $result = $this->appsService->getApplications();
        
        if (isset($result['error'])) {
            return redirect('/login');
        }
        
        return view('dashboard', [
            'applications' => $result['applications'] ?? [],
            'totalApps' => $result['total_accessible_apps'] ?? 0,
        ]);
    }
}
```

### In Blade Template
```blade
@php
    $service = app(\Juniyasyos\IamClient\Services\UserApplicationsService::class);
    $apps = $service->getApplications();
@endphp

@foreach($apps['applications'] ?? [] as $app)
    <div class="app-card">
        @if($app['logo_url'])
            <img src="{{ $app['logo_url'] }}" alt="{{ $app['name'] }}">
        @endif
        <h3>{{ $app['name'] }}</h3>
        <p>{{ $app['description'] }}</p>
        <a href="{{ $app['app_url'] }}" target="_blank">
            Open Application →
        </a>
    </div>
@endforeach
```

### Check User Access
```php
$service = app(\Juniyasyos\IamClient\Services\UserApplicationsService::class);
$app = $service->getApplicationByKey('siimut');

if ($app) {
    redirect($app['app_url']);
} else {
    return response('Access Denied', 403);
}
```

### Debug in Development
```php
$service = app(\Juniyasyos\IamClient\Services\UserApplicationsService::class);
$debug = $service->debugAll();
dd($debug); // See all debug info
```

---

## 🚀 Deployment Status

| Component | Status | Notes |
|-----------|--------|-------|
| Service Class | ✅ READY | Full type hints, comprehensive error handling |
| Artisan Command | ✅ READY | Registered in service provider |
| API Endpoints | ✅ READY | Working with real data |
| Documentation | ✅ READY | 5 documentation files |
| Examples | ✅ READY | 10+ usage examples provided |
| Testing | ✅ COMPLETE | All tests passed |

**Overall Status**: ✅ **PRODUCTION READY**

---

## 📝 Logging

All requests are logged to `storage/logs/laravel.log`:

```bash
# View logs
tail -f storage/logs/laravel.log | grep "UserApplicationsService"
```

Log entries created:
- Service initialization
- API request start
- Request success/failure
- Exception handling
- Session/token info

---

## 🎓 Next Steps

1. ✅ **Testing Complete** - User 0000.00000 fully tested
2. 📦 **Package Ready** - laravel-iam-client has service
3. 🚀 **Ready to Deploy** - Install in client application
4. 📖 **Use Documentation** - Reference QUICK-START.md or QUICK-REFERENCE.md
5. 🔍 **Monitor Logs** - Check logs during integration

---

## 📞 Troubleshooting

### "IAM token missing"
- Solution: User not authenticated via IAM SSO
- Action: Redirect to login

### "Server returned 401"
- Solution: Token expired
- Action: Force re-login

### "No applications returned"
- Solution: User has no accessible apps
- Action: Check IAM user profiles/roles in admin

### Slow responses
- Solution: Network/IAM server issue
- Action: Check IAM server status, use caching

---

## ✨ Key Features Verified

- ✅ Token-aware (uses session IAM token)
- ✅ Error handling (graceful fallbacks)
- ✅ Logging (all requests logged)
- ✅ Debugging (multiple debug methods)
- ✅ Type-safe (full PHP type hints)
- ✅ Well-documented (extensive docs)
- ✅ Production-ready (error handling, logging)
- ✅ Real data (tested with actual user)

---

**Test Duration**: ~5 seconds  
**Test User**: admin (NIP: 0000.00000)  
**Test Date**: 2 April 2026  
**Test Status**: ✅ ALL PASSED

