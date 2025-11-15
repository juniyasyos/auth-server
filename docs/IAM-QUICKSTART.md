# IAM Role Mapping Implementation - Quick Start

## 🎯 What Changed

The IAM system has been completely redesigned to focus on **role-per-application** management instead of granular permissions. This keeps IAM simple and focused while allowing client applications to manage their own permission logic.

## 🏗️ Architecture Overview

### Core Components Created

1. **Domain Models** (`app/Domain/Iam/Models/`)
   - `Application` - Registered client applications
   - `Role` - Roles per application (e.g., `siimut:admin`, `incident:officer`)
   - `UserApplicationRole` - Pivot table for user-role-app assignments

2. **Services** (`app/Domain/Iam/Services/`)
   - `RoleService` - CRUD for roles per application
   - `UserRoleAssignmentService` - Assign/revoke/sync user roles
   - `TokenBuilder` - Build JWT tokens with identity + roles_by_app

3. **Data Transfer Objects** (`app/Domain/Iam/DataTransferObjects/`)
   - `TokenClaims` - Type-safe token payload structure

4. **Controllers** (`app/Domain/Iam/Http/Controllers/`)
   - `SsoTokenController` - SSO token issuance and OAuth2-like endpoints

5. **Config**
   - `config/iam.php` - IAM configuration (issuer, token TTL, signing key)

6. **Database**
   - Migration: `2025_11_14_100000_create_iam_roles_table.php`
   - Migration: `2025_11_14_100001_create_user_application_roles_table.php`
   - Seeder: `IamRolesSeeder` - Seeds roles per application
   - Seeder: `IamUserRoleAssignmentsSeeder` - Assigns roles to users

## 🚀 Getting Started

### Step 1: Run Migrations

```bash
php artisan migrate
```

This creates:
- `roles` table (application-scoped roles)
- `user_application_roles` table (user-role assignments)

### Step 2: Seed Data

```bash
php artisan db:seed
```

This will:
- Create 6 sample users
- Create 4 applications (siimut, tamasuma, incident-report, pharmacy)
- Create roles per application (17 roles total)
- Assign roles to users

### Step 3: Test Token Generation

```bash
php artisan tinker
```

```php
use App\Domain\Iam\Services\TokenBuilder;
use App\Models\User;

$tokenBuilder = app(TokenBuilder::class);
$user = User::where('email', 'admin@gmail.com')->first();

// Build token
$token = $tokenBuilder->buildTokenForUser($user);
echo "Token: $token\n";

// Decode to see claims
$claims = $tokenBuilder->decode($token);
print_r($claims->toPayload());
```

Expected output:
```
Array
(
    [sub] => 1
    [email] => admin@gmail.com
    [name] => Admin User
    [apps] => Array
        (
            [0] => siimut
            [1] => tamasuma
            [2] => incident-report.app
            [3] => pharmacy.app
        )
    [roles_by_app] => Array
        (
            [siimut] => Array
                (
                    [0] => admin
                    [1] => viewer
                )
            [tamasuma] => Array
                (
                    [0] => admin
                )
            [incident-report.app] => Array
                (
                    [0] => admin
                )
            [pharmacy.app] => Array
                (
                    [0] => admin
                )
        )
    [iss] => http://localhost:8000
    [iat] => 1731560000
    [exp] => 1731563600
    [unit] => Administration
)
```

## 📋 Sample Data

### Users and Their Roles

| Email | Apps Access | Roles |
|-------|-------------|-------|
| admin@gmail.com | All 4 apps | siimut: [admin, viewer]<br>tamasuma: [admin]<br>incident: [admin]<br>pharmacy: [admin] |
| doctor@gmail.com | siimut, incident | siimut: [doctor, viewer]<br>incident: [reporter] |
| nurse@gmail.com | siimut, incident | siimut: [nurse, viewer]<br>incident: [reporter] |
| pharmacist@gmail.com | siimut, pharmacy | siimut: [viewer]<br>pharmacy: [pharmacist] |
| manager@gmail.com | siimut, tamasuma, incident | siimut: [viewer]<br>tamasuma: [manager]<br>incident: [officer, viewer] |
| staff@gmail.com | siimut, tamasuma, incident | siimut: [receptionist]<br>tamasuma: [staff]<br>incident: [reporter] |

### Applications

| App Key | Name | Roles Count |
|---------|------|-------------|
| siimut | SIIMUT | 5 (admin, doctor, nurse, receptionist, viewer) |
| tamasuma | Tamasuma | 4 (admin, manager, staff, viewer) |
| incident-report.app | Incident Report | 4 (admin, officer, reporter, viewer) |
| pharmacy.app | Pharmacy | 4 (admin, pharmacist, assistant, viewer) |

## 🔑 API Endpoints

### Issue Token (Authenticated)
```bash
POST /api/sso/token/issue
Authorization: Bearer {session_token}
Content-Type: application/json

Response:
{
  "access_token": "eyJhbGc...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {...},
  "apps": ["siimut", "incident"],
  "roles_by_app": {...}
}
```

### OAuth2 Authorize
```bash
GET /oauth/authorize?client_id=siimut&redirect_uri=...&response_type=code
```

### OAuth2 Token Exchange
```bash
POST /api/sso/token
{
  "grant_type": "authorization_code",
  "client_id": "siimut",
  "client_secret": "siimut_secret_key_123",
  "code": "...",
  "redirect_uri": "..."
}
```

### Token Introspection
```bash
POST /api/sso/introspect
{
  "token": "eyJhbGc..."
}
```

### User Info
```bash
GET /api/sso/userinfo
Authorization: Bearer {access_token}
```

## 💻 Using Services

### RoleService

```php
use App\Domain\Iam\Services\RoleService;

$roleService = app(RoleService::class);

// Create a role
$role = $roleService->createRole(
    appKey: 'siimut',
    slug: 'admin',
    name: 'Administrator',
    description: 'Full access',
    isSystem: true
);

// Get roles for app
$roles = $roleService->getRolesForApplication('siimut');

// Find specific role
$role = $roleService->findRoleBySlug('siimut', 'admin');
```

### UserRoleAssignmentService

```php
use App\Domain\Iam\Services\UserRoleAssignmentService;

$assignmentService = app(UserRoleAssignmentService::class);

// Assign role to user
$assignmentService->assignRoleToUser($user, $role);

// Sync roles (replace all roles for user in app)
$assignmentService->syncRolesForUserAndApp(
    user: $user,
    app: $application,
    roleSlugs: ['admin', 'viewer']
);

// Get user's roles by app
$rolesByApp = $assignmentService->getRolesByAppForUser($user);
// Returns: ['siimut' => ['admin'], 'incident' => ['officer']]

// Get accessible apps
$apps = $assignmentService->getAppsForUser($user);
// Returns: ['siimut', 'incident']
```

### TokenBuilder

```php
use App\Domain\Iam\Services\TokenBuilder;

$tokenBuilder = app(TokenBuilder::class);

// Build token for user
$token = $tokenBuilder->buildTokenForUser($user);

// Verify token
$claims = $tokenBuilder->verify($token);

// Decode without verification
$claims = $tokenBuilder->decode($token);

// Refresh token
$newToken = $tokenBuilder->refresh($oldToken);

// Check validity
$isValid = $tokenBuilder->isValid($token);
```

## 📚 Documentation

Full documentation available in:
- **Architecture**: `docs/iam-role-mapping.md`
- **Token Structure**: `docs/iam-token-structure.md`

## 🔄 Migration from Spatie Permission

If you have existing Spatie Permission data:

1. **Keep Spatie tables** for now (don't drop them)
2. **Run new migrations** to create IAM tables
3. **Map existing roles** to application-scoped roles
4. **Migrate assignments** from Spatie to IAM structure
5. **Update token generation** to use TokenBuilder
6. **Test thoroughly** before removing Spatie

## ⚙️ Configuration

Edit `config/iam.php`:

```php
return [
    'issuer' => env('IAM_ISSUER', 'https://iam.local'),
    'token_ttl' => env('IAM_TOKEN_TTL', 3600),
    'signing_key' => env('IAM_SIGNING_KEY', env('APP_KEY')),
    'algorithm' => env('IAM_JWT_ALGORITHM', 'HS256'),
];
```

Environment variables:
```env
IAM_ISSUER=https://iam.rsch.local
IAM_TOKEN_TTL=3600
IAM_SIGNING_KEY=your-secret-key
```

## 🧪 Testing

```bash
# Test token issuance
curl -X POST http://localhost:8000/api/sso/token/issue \
  -H "Authorization: Bearer {session_token}" \
  -H "Content-Type: application/json"

# Test introspection
curl -X POST http://localhost:8000/api/sso/introspect \
  -H "Content-Type: application/json" \
  -d '{"token": "eyJhbGc..."}'
```

## ✅ What's Next

1. **Filament Resources** - Create admin UI for managing roles and assignments
2. **Client Integration** - Implement role-to-permission mapping in client apps
3. **Middleware** - Update existing middleware to use new token structure
4. **Testing Routes** - Create testing endpoints to validate token generation
5. **API Documentation** - Generate OpenAPI/Swagger docs

## 🆘 Troubleshooting

### Token generation fails
- Check `config/iam.php` is loaded: `php artisan config:cache`
- Verify signing key is set: `php artisan config:show iam`
- Ensure user has roles assigned

### Roles not found
- Run seeders: `php artisan db:seed --class=IamRolesSeeder`
- Check application exists: `Application::where('app_key', 'siimut')->first()`

### Empty roles_by_app in token
- Verify user has role assignments: `UserApplicationRole::where('user_id', $userId)->get()`
- Check relationships are loaded correctly

## 📞 Support

For questions or issues:
1. Check documentation: `docs/iam-role-mapping.md`
2. Review token structure: `docs/iam-token-structure.md`
3. Test with tinker to isolate issues
