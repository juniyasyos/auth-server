# IAM Token Structure

## Overview

The IAM system issues **JWT (JSON Web Token)** for Single Sign-On (SSO) authentication. The token contains user identity and role assignments per application, but **NOT** granular permissions.

## Token Format

Tokens are encoded using **JWT (JSON Web Token)** standard with the following characteristics:

- **Algorithm**: HS256 (HMAC SHA-256) by default, configurable
- **Signing Key**: Configured in `config/iam.php` or `IAM_SIGNING_KEY` env variable
- **Format**: `header.payload.signature` (standard JWT)

## Token Payload Structure

### Required Claims

| Claim | Type | Description | Example |
|-------|------|-------------|---------|
| `sub` | integer | User ID in IAM system | `12` |
| `email` | string | User email address | `"user@example.com"` |
| `name` | string | User full name | `"Dr. John Doe"` |
| `apps` | array | Array of app_keys user has access to | `["siimut", "incident"]` |
| `roles_by_app` | object | Map of app_key → array of role slugs | See below |
| `iss` | string | Token issuer (IAM URL) | `"https://iam.rsch.local"` |
| `iat` | integer | Issued at timestamp (Unix) | `1731560000` |
| `exp` | integer | Expiry timestamp (Unix) | `1731563600` |

### Optional Claims

| Claim | Type | Description | Example |
|-------|------|-------------|---------|
| `unit` | string | User's organizational unit/department | `"Emergency Room"` |
| `employee_id` | string | Internal employee identifier | `"EMP-12345"` |
| `extra` | object | Additional custom claims | `{"department": "ICU"}` |

### Roles By App Structure

The `roles_by_app` claim is a key-value map where:
- **Key**: Application app_key
- **Value**: Array of role slugs the user has in that application

```json
{
  "roles_by_app": {
    "siimut": ["admin", "viewer"],
    "incident": ["officer"],
    "pharmacy": ["pharmacist"]
  }
}
```

## Complete Token Example

### Decoded Payload

```json
{
  "sub": 12,
  "email": "dr.john@example.com",
  "name": "Dr. John Doe",
  "apps": ["siimut", "incident", "pharmacy"],
  "roles_by_app": {
    "siimut": ["admin", "viewer"],
    "incident": ["officer"],
    "pharmacy": ["pharmacist"]
  },
  "unit": "Emergency Room",
  "employee_id": "EMP-2024-001",
  "iss": "https://iam.rsch.local",
  "iat": 1731560000,
  "exp": 1731563600
}
```

### Encoded JWT

```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjEyLCJlbWFpbCI6ImRyLmpvaG5AZXhhbXBsZS5jb20iLCJuYW1lIjoiRHIuIEpvaG4gRG9lIiwiYXBwcyI6WyJzaWltdXQiLCJpbmNpZGVudCIsInBoYXJtYWN5Il0sInJvbGVzX2J5X2FwcCI6eyJzaWltdXQiOlsiYWRtaW4iLCJ2aWV3ZXIiXSwiaW5jaWRlbnQiOlsib2ZmaWNlciJdLCJwaGFybWFjeSI6WyJwaGFybWFjaXN0Il19LCJ1bml0IjoiRW1lcmdlbmN5IFJvb20iLCJlbXBsb3llZV9pZCI6IkVNUC0yMDI0LTAwMSIsImlzcyI6Imh0dHBzOi8vaWFtLnJzY2gubG9jYWwiLCJpYXQiOjE3MzE1NjAwMDAsImV4cCI6MTczMTU2MzYwMH0.SIGNATURE_HERE
```

## Token Lifecycle

### 1. Token Generation

```php
use App\Domain\Iam\Services\TokenBuilder;

$tokenBuilder = app(TokenBuilder::class);

// Build token for user
$accessToken = $tokenBuilder->buildTokenForUser($user);

// Or build claims first, then encode
$claims = $tokenBuilder->buildClaimsForUser($user);
$accessToken = $tokenBuilder->encode($claims);
```

### 2. Token Verification

```php
try {
    $claims = $tokenBuilder->verify($token);
    
    // Access token data
    echo $claims->userId;      // 12
    echo $claims->email;       // "dr.john@example.com"
    print_r($claims->apps);    // ["siimut", "incident"]
    
} catch (\Exception $e) {
    // Token invalid or expired
}
```

### 3. Token Refresh

```php
// Refresh an existing token (generates new token with updated expiry)
$newToken = $tokenBuilder->refresh($oldToken);
```

### 4. Token Introspection

```php
// Check token validity without throwing exceptions
$isValid = $tokenBuilder->isValid($token);

// Decode without verification (use carefully!)
$claims = $tokenBuilder->decode($token);
```

## Token Claims Object

The `TokenClaims` DTO provides convenient methods:

```php
use App\Domain\Iam\DataTransferObjects\TokenClaims;

$claims = $tokenBuilder->verify($token);

// Check expiry
$claims->isExpired();              // false
$claims->getTimeUntilExpiry();     // 3600 (seconds)

// Check app access
$claims->hasAccessToApp('siimut'); // true

// Get roles for app
$claims->getRolesForApp('siimut'); // ['admin', 'viewer']

// Check specific role
$claims->hasRoleInApp('siimut', 'admin'); // true

// Convert back to array
$payload = $claims->toPayload();
```

## Important: What's NOT in the Token

The IAM token intentionally **does NOT include**:

- ❌ Granular permissions (e.g., `patient.view`, `report.create`)
- ❌ Permission lists
- ❌ Application-specific business data
- ❌ User preferences or settings
- ❌ Session data

**Why?** 
- Keeps token lightweight
- Prevents token bloat
- Allows applications to independently manage permissions
- Reduces coupling between IAM and client applications

## Client Application Usage

### Receiving and Parsing Token

```php
// In client application

// 1. Receive token from IAM (via SSO flow or API)
$token = request()->bearerToken();

// 2. Verify token (validate signature and expiry)
try {
    $claims = $tokenBuilder->verify($token);
} catch (\Exception $e) {
    return response()->json(['error' => 'Unauthorized'], 401);
}

// 3. Check if user has access to THIS application
if (!$claims->hasAccessToApp('siimut')) {
    return response()->json(['error' => 'No access to this application'], 403);
}

// 4. Get user's roles in THIS application
$userRoles = $claims->getRolesForApp('siimut'); // ['admin', 'viewer']

// 5. Map roles to local permissions (client-side logic)
$permissions = $this->mapRolesToPermissions($userRoles);

// 6. Store in session or context for request
session(['user_id' => $claims->userId]);
session(['permissions' => $permissions]);
```

### Mapping Roles to Permissions (Client Side)

```php
// In client application (e.g., SIIMUT)

class PermissionMapper
{
    private array $rolePermissionMap = [
        'admin' => ['*'], // All permissions
        'doctor' => [
            'patient.view',
            'patient.create',
            'patient.edit',
            'report.view',
            'report.create',
            'prescription.create',
        ],
        'nurse' => [
            'patient.view',
            'patient.edit',
            'report.view',
        ],
        'viewer' => [
            'patient.view',
            'report.view',
        ],
    ];

    public function getPermissionsForRoles(array $roles): array
    {
        $permissions = [];
        
        foreach ($roles as $role) {
            if (isset($this->rolePermissionMap[$role])) {
                $permissions = array_merge(
                    $permissions, 
                    $this->rolePermissionMap[$role]
                );
            }
        }
        
        return array_unique($permissions);
    }

    public function hasPermission(array $permissions, string $permission): bool
    {
        // Check for wildcard
        if (in_array('*', $permissions)) {
            return true;
        }
        
        return in_array($permission, $permissions);
    }
}

// Usage
$mapper = new PermissionMapper();
$userRoles = $claims->getRolesForApp('siimut');
$permissions = $mapper->getPermissionsForRoles($userRoles);

if ($mapper->hasPermission($permissions, 'patient.view')) {
    // Allow access
}
```

## Token Validation Flow

```mermaid
sequenceDiagram
    participant Client
    participant IAM
    participant User

    User->>IAM: Login
    IAM->>IAM: Authenticate
    IAM->>IAM: Query user roles via UserRoleAssignmentService
    IAM->>IAM: Build TokenClaims (id, email, apps, roles_by_app)
    IAM->>IAM: Encode JWT
    IAM->>Client: Return access_token
    Client->>Client: Verify token signature
    Client->>Client: Check expiry
    Client->>Client: Extract roles_by_app[app_key]
    Client->>Client: Map roles to local permissions
    Client->>User: Grant access with permissions
```

## Configuration

### Environment Variables

```env
# Issuer URL (included in iss claim)
IAM_ISSUER=https://iam.rsch.local

# Token time-to-live (seconds)
IAM_TOKEN_TTL=3600

# Signing key (falls back to APP_KEY)
IAM_SIGNING_KEY=your-secret-signing-key

# Algorithm
IAM_JWT_ALGORITHM=HS256
```

### Config File: `config/iam.php`

```php
return [
    'issuer' => env('IAM_ISSUER', env('APP_URL')),
    'token_ttl' => env('IAM_TOKEN_TTL', 3600),
    'signing_key' => env('IAM_SIGNING_KEY', env('APP_KEY')),
    'algorithm' => env('IAM_JWT_ALGORITHM', 'HS256'),
];
```

## Security Considerations

### DO's ✅

- ✅ Always verify token signature
- ✅ Check token expiry (`exp` claim)
- ✅ Validate issuer (`iss` claim) matches expected IAM URL
- ✅ Use HTTPS for all IAM communication
- ✅ Rotate signing keys periodically
- ✅ Implement token refresh mechanism
- ✅ Store tokens securely (httpOnly cookies or secure storage)

### DON'Ts ❌

- ❌ Don't store tokens in localStorage (XSS risk)
- ❌ Don't expose signing key
- ❌ Don't trust token without verification
- ❌ Don't put sensitive data in token (it's not encrypted)
- ❌ Don't use tokens after expiry
- ❌ Don't share tokens between applications (validate app_key)

## Token Expiry and Refresh

### Access Token Lifecycle

1. **Issue**: IAM generates token with TTL (default 1 hour)
2. **Use**: Client uses token for authenticated requests
3. **Expire**: Token becomes invalid after TTL
4. **Refresh**: Client requests new token via refresh flow

### Refresh Flow

```php
// Client detects token expiring soon
if ($claims->getTimeUntilExpiry() < 300) { // Less than 5 minutes
    // Request refresh
    $newToken = Http::post('https://iam.rsch.local/api/token/refresh', [
        'token' => $currentToken,
    ])->json('access_token');
    
    // Update stored token
    session(['access_token' => $newToken]);
}
```

## Debugging Tokens

### Decode Token Manually

```bash
# Decode JWT without verification (for debugging only)
php artisan tinker

>>> $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...";
>>> $parts = explode('.', $token);
>>> $payload = json_decode(base64_decode($parts[1]), true);
>>> print_r($payload);
```

### Verify Token via API

```bash
curl -X POST https://iam.rsch.local/api/sso/introspect \
  -H "Content-Type: application/json" \
  -d '{"token": "YOUR_JWT_TOKEN"}'
```

## Common Scenarios

### Scenario 1: User with Single Application Access

```json
{
  "sub": 5,
  "email": "nurse@example.com",
  "name": "Jane Nurse",
  "apps": ["siimut"],
  "roles_by_app": {
    "siimut": ["nurse"]
  },
  "unit": "ICU",
  "iss": "https://iam.rsch.local",
  "iat": 1731560000,
  "exp": 1731563600
}
```

### Scenario 2: User with Multiple Applications

```json
{
  "sub": 3,
  "email": "admin@example.com",
  "name": "System Admin",
  "apps": ["siimut", "incident", "pharmacy"],
  "roles_by_app": {
    "siimut": ["admin"],
    "incident": ["admin"],
    "pharmacy": ["admin"]
  },
  "unit": "IT Department",
  "iss": "https://iam.rsch.local",
  "iat": 1731560000,
  "exp": 1731563600
}
```

### Scenario 3: User with Multiple Roles in One App

```json
{
  "sub": 8,
  "email": "doctor@example.com",
  "name": "Dr. Sarah Smith",
  "apps": ["siimut"],
  "roles_by_app": {
    "siimut": ["doctor", "admin", "viewer"]
  },
  "unit": "Cardiology",
  "iss": "https://iam.rsch.local",
  "iat": 1731560000,
  "exp": 1731563600
}
```

## API Endpoints for Token Operations

### Issue Token (POST /api/sso/token/issue)

```bash
curl -X POST https://iam.rsch.local/api/sso/token/issue \
  -H "Authorization: Bearer SESSION_TOKEN" \
  -H "Content-Type: application/json"
```

Response:
```json
{
  "access_token": "eyJhbGc...",
  "token_type": "Bearer",
  "expires_in": 3600,
  "user": {
    "id": 12,
    "name": "Dr. John Doe",
    "email": "dr.john@example.com"
  },
  "apps": ["siimut", "incident"],
  "roles_by_app": {
    "siimut": ["admin"],
    "incident": ["officer"]
  }
}
```

### Introspect Token (POST /api/sso/introspect)

```bash
curl -X POST https://iam.rsch.local/api/sso/introspect \
  -H "Content-Type: application/json" \
  -d '{"token": "eyJhbGc..."}'
```

Response:
```json
{
  "active": true,
  "sub": 12,
  "email": "dr.john@example.com",
  "name": "Dr. John Doe",
  "apps": ["siimut"],
  "roles_by_app": {"siimut": ["admin"]},
  "iss": "https://iam.rsch.local",
  "iat": 1731560000,
  "exp": 1731563600
}
```

### User Info (GET /api/sso/userinfo)

```bash
curl -X GET https://iam.rsch.local/api/sso/userinfo \
  -H "Authorization: Bearer eyJhbGc..."
```

Response:
```json
{
  "sub": 12,
  "email": "dr.john@example.com",
  "name": "Dr. John Doe",
  "unit": "Emergency Room",
  "apps": ["siimut"],
  "roles_by_app": {"siimut": ["admin"]}
}
```

## Best Practices

1. **Token Size**: Keep tokens small by avoiding unnecessary claims
2. **Short Expiry**: Use short TTL (1 hour) and implement refresh
3. **Secure Storage**: Store tokens in httpOnly cookies or secure storage
4. **Signature Verification**: Always verify signature before trusting claims
5. **App Validation**: Client should verify they're in the `apps` array
6. **Role Caching**: Cache role-permission mappings to reduce processing
7. **Audit Trail**: Log token issuance and usage for security auditing

## See Also

- [IAM Role Mapping Architecture](./iam-role-mapping.md)
- [Client Integration Guide](./CLIENT-INTEGRATION.md)
- Config: `config/iam.php`
