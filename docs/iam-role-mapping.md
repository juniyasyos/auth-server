# IAM Role Mapping Architecture

## Overview

The Identity & Access Management (IAM) system manages **user identity, applications, and role assignments per application**. The IAM does **NOT** store or manage granular permissions that belong to client applications.

## Core Principles

1. **IAM is the source of truth for:**
   - Centralized user data
   - Application registry
   - Role definitions per application
   - User ↔ Role ↔ Application mappings

2. **IAM does NOT manage:**
   - Granular permissions specific to applications
   - Application-specific business logic
   - Permission-to-feature mappings

3. **Client applications are responsible for:**
   - Defining their own permission sets
   - Mapping IAM roles to local permissions
   - Enforcing permission-based access control

## Database Schema

### Applications Table

Stores registered client applications that integrate with the IAM system.

```sql
applications
├── id (PK)
├── app_key (unique identifier, e.g., "siimut", "incident")
├── name (human-readable name)
├── description (optional)
├── enabled (boolean, application active status)
├── redirect_uris (JSON array, OAuth redirect URIs)
├── callback_url (optional)
├── secret (hashed, for client authentication)
├── logo_url (optional)
├── token_expiry (integer, custom TTL in seconds)
├── created_by (FK to users)
├── timestamps
└── soft_deletes
```

### Roles Table

Defines roles per application. Each role belongs to exactly one application.

```sql
roles
├── id (PK)
├── application_id (FK to applications, cascade on delete)
├── slug (role identifier within app, e.g., "admin", "viewer")
├── name (human-readable, e.g., "Admin SIIMUT", "Viewer Incident")
├── description (optional, explains role purpose)
├── is_system (boolean, protected system role)
├── timestamps
└── UNIQUE INDEX (application_id, slug)
```

**Key Points:**
- `slug` must be unique per application
- System roles (`is_system = true`) are protected from deletion and slug changes
- Roles are application-scoped, not global

### User Application Roles Table

Pivot table mapping users to roles in specific applications.

```sql
user_application_roles
├── id (PK)
├── user_id (FK to users, cascade on delete)
├── role_id (FK to roles, cascade on delete)
├── assigned_by (FK to users, nullable, tracks who assigned)
├── timestamps
└── UNIQUE INDEX (user_id, role_id)
```

**Key Points:**
- A user can have multiple roles in an application
- A user can have roles in multiple applications
- Each user-role pair can only exist once
- Tracks who assigned the role for audit purposes

## Domain Model Relationships

```
Application (1) ─── (N) Role (N) ─── (N) User
                          ↓
                UserApplicationRole
                    (pivot table)
```

### Application Model

```php
// Get all roles for this application
$application->roles();

// Get only system roles
$application->systemRoles();

// Check redirect URI validity
$application->isValidRedirectUri($uri);
```

### Role Model

```php
// Get the application this role belongs to
$role->application();

// Get all users with this role
$role->users();

// Check if system role
$role->isSystemRole();

// Get identifier (app_key:slug)
$role->identifier; // e.g., "siimut:admin"
```

### User Model

```php
// Get all roles assigned to user
$user->applicationRoles();

// Get roles grouped by app
$user->rolesByApp();
// Returns: ['siimut' => ['admin', 'viewer'], 'incident' => ['officer']]

// Get list of accessible apps
$user->accessibleApps();
// Returns: ['siimut', 'incident', 'pharmacy']
```

## Services

### RoleService

Manages CRUD operations for roles per application.

```php
use App\Domain\Iam\Services\RoleService;

$roleService = app(RoleService::class);

// Create a new role
$role = $roleService->createRole(
    appKey: 'siimut',
    slug: 'admin',
    name: 'Administrator SIIMUT',
    description: 'Full access to SIIMUT system',
    isSystem: false
);

// Update a role
$roleService->updateRole($role, [
    'name' => 'Updated Admin Name',
    'description' => 'New description'
]);

// Delete a role (cannot delete system roles or roles with users)
$roleService->deleteRole($role);

// Get all roles for an application
$roles = $roleService->getRolesForApplication('siimut');

// Find specific role
$role = $roleService->findRoleBySlug('siimut', 'admin');
```

### UserRoleAssignmentService

Manages user-role assignments and queries.

```php
use App\Domain\Iam\Services\UserRoleAssignmentService;

$assignmentService = app(UserRoleAssignmentService::class);

// Assign a role to a user
$assignmentService->assignRoleToUser($user, $role, $adminUser);

// Revoke a role from a user
$assignmentService->revokeRoleFromUser($user, $role);

// Sync roles for a user in an application (replace all)
$assignmentService->syncRolesForUserAndApp(
    user: $user,
    app: $application,
    roleSlugs: ['admin', 'viewer'],
    assignedBy: $adminUser
);

// Get user's roles by application
$rolesByApp = $assignmentService->getRolesByAppForUser($user);
// Returns: ['siimut' => ['admin'], 'incident' => ['officer', 'viewer']]

// Get applications user has access to
$apps = $assignmentService->getAppsForUser($user);
// Returns: ['siimut', 'incident']

// Check if user has a specific role
$hasRole = $assignmentService->userHasRole($user, 'siimut', 'admin');

// Revoke all roles for user in an application
$assignmentService->revokeAllRolesForUserInApp($user, $application);
```

## How IAM Admin Assigns Roles

### Admin Workflow

1. **View available applications**: Admin sees registered applications
2. **Select user**: Admin selects which user to configure
3. **For each application**:
   - View available roles for that application
   - Assign/remove roles to the user
4. **Save**: User-role mappings are persisted

### Example Admin Operations

```php
// Admin wants to give user access to SIIMUT with admin + viewer roles
$siimut = Application::findByKey('siimut');
$adminRole = $roleService->findRoleBySlug('siimut', 'admin');
$viewerRole = $roleService->findRoleBySlug('siimut', 'viewer');

$assignmentService->assignRoleToUser($user, $adminRole, $admin);
$assignmentService->assignRoleToUser($user, $viewerRole, $admin);

// Or sync all at once
$assignmentService->syncRolesForUserAndApp(
    user: $user,
    app: $siimut,
    roleSlugs: ['admin', 'viewer'],
    assignedBy: $admin
);
```

## IAM vs Client Application Responsibilities

### IAM Responsibilities
- ✅ Store and manage user accounts
- ✅ Register applications
- ✅ Define roles per application
- ✅ Assign roles to users
- ✅ Issue SSO tokens with identity + roles_by_app
- ✅ Authenticate users
- ❌ **NOT** managing granular permissions

### Client Application Responsibilities
- ✅ Define local permissions (e.g., `patient.view`, `report.create`)
- ✅ Map IAM roles to local permissions
  ```php
  // In client application
  $rolePermissions = [
      'admin' => ['*'], // All permissions
      'viewer' => ['patient.view', 'report.view'],
      'officer' => ['report.create', 'report.edit'],
  ];
  ```
- ✅ Enforce permission checks
- ✅ Use IAM token to identify user and their roles
- ❌ **NOT** managing user authentication (handled by IAM)

## Example: Client Application Integration

### Step 1: Client Receives SSO Token

Client receives token from IAM with payload:
```json
{
  "sub": 12,
  "email": "user@example.com",
  "name": "John Doe",
  "apps": ["siimut", "incident"],
  "roles_by_app": {
    "siimut": ["admin", "viewer"],
    "incident": ["officer"]
  },
  "unit": "Emergency Room",
  "iss": "https://iam.rsch.local",
  "iat": 1731560000,
  "exp": 1731563600
}
```

### Step 2: Client Maps Roles to Permissions

```php
// In SIIMUT application
$userRoles = $token['roles_by_app']['siimut']; // ['admin', 'viewer']

$permissions = [];
foreach ($userRoles as $role) {
    $permissions = array_merge($permissions, $rolePermissionMap[$role]);
}

// Now check permissions
if (in_array('patient.view', $permissions)) {
    // Allow access
}
```

### Step 3: Client Updates Local Cache

Client can cache user's roles and permissions for the session, refreshing when token expires.

## Advantages of This Design

1. **Separation of Concerns**: IAM focuses on identity and application access, clients handle business logic
2. **Flexibility**: Each application defines permissions that make sense for its domain
3. **Maintainability**: Adding new permissions doesn't require IAM changes
4. **Scalability**: New applications can be added without bloating IAM
5. **Security**: IAM remains simple and focused, reducing attack surface
6. **Autonomy**: Applications can evolve their permission model independently

## Migration from Spatie Permission

If migrating from Spatie Permission-based system:

1. **Analyze existing permissions**: Group by application context
2. **Create application-scoped roles**: Convert global roles to app-specific roles
3. **Map users to new roles**: Transfer role assignments to new structure
4. **Update token generation**: Use new TokenBuilder instead of Spatie traits
5. **Client-side mapping**: Implement role → permission mapping in each client

## Common Patterns

### Pattern 1: Multi-Application User

User has roles in multiple applications:
```php
User: dr.john@example.com
  - SIIMUT: ['doctor', 'admin']
  - Incident Report: ['reporter']
  - Pharmacy: ['viewer']
```

Token will contain all apps and roles, client filters for its app_key.

### Pattern 2: System Roles

Protected roles that can't be deleted or modified:
```php
$roleService->createRole(
    appKey: 'siimut',
    slug: 'super_admin',
    name: 'Super Administrator',
    isSystem: true // Protected
);
```

### Pattern 3: Role Hierarchy (Client-Side)

IAM doesn't enforce hierarchy, but clients can:
```php
// In client application
$roleHierarchy = [
    'admin' => ['manager', 'staff', 'viewer'],
    'manager' => ['staff', 'viewer'],
    'staff' => ['viewer'],
];

function hasRoleOrHigher($userRoles, $requiredRole) {
    foreach ($userRoles as $role) {
        if ($role === $requiredRole || 
            in_array($requiredRole, $roleHierarchy[$role] ?? [])) {
            return true;
        }
    }
    return false;
}
```

## Best Practices

1. **Use descriptive slugs**: `admin`, `viewer`, `editor`, not `role1`, `role2`
2. **Document role purpose**: Use description field to explain what each role does
3. **Protect system roles**: Mark critical roles as `is_system = true`
4. **Audit assignments**: Use `assigned_by` field for audit trail
5. **Sync not individual assigns**: Use `syncRolesForUserAndApp` for bulk updates
6. **Cache strategically**: Client applications should cache role-permission mappings
7. **Token refresh**: Implement token refresh to update user roles without re-login

## See Also

- [IAM Token Structure](./iam-token-structure.md)
- [Client Integration Guide](./CLIENT-INTEGRATION.md)
- Config: `config/iam.php`
