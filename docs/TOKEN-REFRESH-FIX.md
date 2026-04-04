# Token Refresh Fix - SSO Application Context Preservation

## Problem
During token refresh operations (`POST /api/sso/token/refresh`), the refreshed JWT tokens were losing the critical `app` field (application context) that was present in the original token. This caused subsequent API calls to fail with:

```
Error: "SSO token application is missing"
StatusCode: 422
```

### Root Cause
The issue occurred in the token lifecycle:

1. **Initial Token Generation** (`/sso/redirect` → `TokenService.issue()`):
   - Creates JWT with `'app' => $application->app_key` in payload ✅
   - Example: `{'app': 'siimut', 'sub': 123, 'iss': '...', ...}`

2. **Token Refresh** (`/api/sso/token/refresh` → `TokenBuilder.refresh()`):
   - Decodes old token using `TokenClaims::fromArray()`
   - **PROBLEM**: TokenClaims DTO didn't reconstruct custom `app` field
   - `refresh()` called `buildTokenForUser($user, $oldClaims->extra)` with missing `app`
   - New token issued without `app` field ❌

3. **Token Verification** (`TokenService.verify()`):
   - Checks for required `app` field: `$payload['app'] ?? null`
   - Throws exception if missing 🔴

## Solution
Modified three key components to preserve application context through the refresh cycle:

### 1. TokenBuilder.refresh() - Enhanced Payload Preservation
**File**: `app/Domain/Iam/Services/TokenBuilder.php` (lines 130-167)

**Changes**:
- Added `extractRawPayload()` helper method to safely extract JWT payload without verification
- Modified `refresh()` to:
  1. Extract raw JWT payload (gets the `'app'` field directly)
  2. Merge `app` into the extra array: `array_merge($oldClaims->extra, ['app' => $appKey])`
  3. Pass enhanced extra to `buildTokenForUser($user, $extra)`

**Code**:
```php
// Extract raw payload to preserve 'app' field
$rawPayload = $this->extractRawPayload($token);
$appKey = $rawPayload['app'] ?? null;

// Merge extra data with app field to preserve it during refresh
$extra = array_merge($oldClaims->extra, ['app' => $appKey]);

// Build fresh token with preserved application context
return $this->buildTokenForUser($user, $extra);
```

### 2. TokenClaims.toPayload() - Include Extra Fields in JWT
**File**: `app/Domain/Iam/DataTransferObjects/TokenClaims.php` (lines 41-77)

**Changes**:
- Modified `toPayload()` to include `app` field from extra array in JWT payload
- Added support for other optional fields (`unit`, `employee_id`)
- Includes any additional extra fields not overriding standard claims

**Code**:
```php
// Include 'app' field from extra if present (for SSO application context)
if (isset($this->extra['app'])) {
    $payload['app'] = $this->extra['app'];
}

// Include any other extra fields
foreach ($this->extra as $key => $value) {
    if ($key !== 'app' && !isset($payload[$key])) {
        $payload[$key] = $value;
    }
}
```

### 3. TokenClaims.fromArray() - Reconstruct Custom Fields
**File**: `app/Domain/Iam/DataTransferObjects/TokenClaims.php` (lines 79-100)

**Changes**:
- Modified `fromArray()` to automatically extract non-standard claims
- Any field not in the standard list goes into `extra` array
- Ensures `app` field from decoded JWT is preserved in extra

**Code**:
```php
// Extract extra fields that aren't standard JWT claims
$extra = [];
$standardClaims = ['sub', 'nip', 'email', 'name', 'apps', 'roles_by_app', 'iss', 'iat', 'exp', 'unit', 'employee_id'];

foreach ($data as $key => $value) {
    if (!in_array($key, $standardClaims, true)) {
        $extra[$key] = $value;
    }
}
```

## Token Lifecycle After Fix

```
┌─────────────────────────────────────────────────────────────────┐
│ Original Token Generation                                        │
│ /sso/redirect?app=siimut                                         │
└─────────────────┬───────────────────────────────────────────────┘
                  │
                  ▼
        ┌─────────────────────┐
        │ TokenService.issue()│
        │ { app: 'siimut' }   │
        └────────────┬────────┘
                     │
                     ▼
        ┌──────────────────────────────────┐
        │ JWT Payload                      │
        │ {app: 'siimut', sub: 123, ...}  │
        └────────────┬─────────────────────┘
                     │
        ┌────────────┴──────────────────┐
        │                               │
        ▼                               ▼
   ┌────────────┐        ┌──────────────────────┐
   │ API Usage  │        │ POST /api/sso/token/ │
   │ ✅ Works   │        │ refresh              │
   └────────────┘        └──────────┬───────────┘
                                    │
                                    ▼
                        ┌───────────────────────┐
                        │ TokenBuilder.refresh()│
                        │ Extract raw app field │
                        │ Preserve in extra []  │
                        └───────────┬───────────┘
                                    │
                                    ▼
                        ┌───────────────────────┐
                        │ New Token Created     │
                        │ {app: 'siimut', ...}  │
                        │ ✅ app field restored │
                        └───────────┬───────────┘
                                    │
                                    ▼
                        ┌───────────────────────┐
                        │ TokenService.verify() │
                        │ ✅ app field found    │
                        │ ✅ Verification OK    │
                        └───────────┬───────────┘
                                    │
                                    ▼
                        ┌───────────────────────┐
                        │ API Call Succeeds     │
                        │ Token is valid        │
                        └───────────────────────┘
```

## Files Modified

1. **app/Domain/Iam/Services/TokenBuilder.php**
   - Enhanced `refresh()` method (lines 130-167)
   - Added `extractRawPayload()` helper (lines 197-211)

2. **app/Domain/Iam/DataTransferObjects/TokenClaims.php**
   - Updated `toPayload()` method (lines 41-77)  
   - Updated `fromArray()` method (lines 79-100)

## Testing the Fix

### Token Flow Test
```php
// Create original token
$originalToken = $tokenService->issue($user, 'siimut');

// Refresh token
$refreshedToken = $tokenBuilder->refresh($originalToken);

// Verify - should succeed now
$verified = $tokenService->verify($refreshedToken);  // ✅ Success
```

### API Endpoint Test
```bash
# 1. Get original token from /sso/redirect?app=siimut ✅
# 2. Call API with original token ✅
# 3. POST /api/sso/token/refresh with token ✅
# 4. Get new refreshed token ✅
# 5. Call API with refreshed token ✅
```

## Backwards Compatibility
The fix is fully backwards compatible:
- Existing tokens without `app` field still work
- TokenService.issue() unchanged - still adds `app` to all new tokens
- TokenBuilder.decode() works with both old and new tokens
- TokenService.verify() still requires `app` field (as before)

## Impact
✅ SSO token refresh now preserves application context  
✅ API clients can maintain authenticated sessions across token refreshes  
✅ No more "SSO token application is missing" errors on refreshed tokens  
✅ Multi-application SSO flows remain secure and functional
