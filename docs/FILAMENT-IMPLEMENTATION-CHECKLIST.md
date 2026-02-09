# Filament UX Optimization - Implementation Checklist

**Prioritas:** Quick Wins First → Medium Effort → Large Refactor  
**Timeline:** Phase in langsung atau per sprint

---

## 🚀 PHASE 1: QUICK WINS (Do ASAP - 2-3 jam)

### ✅ Task 1.1: Standardize Navigation Group

**File:** `app/Filament/Panel/Resources/*/[ResourceName]Resource.php`

**Current State (Inconsistent):**
```php
// AccessProfileResource.php
protected static string|UnitEnum|null $navigationGroup = 'IAM Management';

// ApplicationResource.php  
protected static string | UnitEnum | null $navigationGroup = 'IAM Management';

// UserResource.php
protected static string | UnitEnum | null $navigationGroup = 'IAM Management';

// RoleResource.php
protected static string | UnitEnum | null $navigationGroup = 'IAM';  // ← DIFFERENT!
```

**Action:** Standardize ke satu pilihan:

```php
// CHOICE A: "IAM Management" (more descriptive)
protected static string|UnitEnum|null $navigationGroup = 'IAM Management';

// CHOICE B: "Identity & Access" (more modern)
protected static string|UnitEnum|null $navigationGroup = 'Identity & Access';

// CHOICE C: "Administration" (broader)
protected static string|UnitEnum|null $navigationGroup = 'Administration';
```

**Estimated:** 5 menit

---

### ✅ Task 1.2: Hide or Mark Spatie Roles Resource

**Files:**
- `app/Filament/Panel/Resources/Roles/RoleResource.php`
- `PanelProvider.php` or equivalent config

**Option A: Hide from Navigation (Recommended)**

```php
// app/Filament/Panel/Resources/Roles/RoleResource.php

class RoleResource extends Resource
{
    // ... existing code ...

    protected static bool $shouldRegisterNavigation = false;  // ← HIDE from menu
    
    // But still accessible for advanced use: /admin/roles
}
```

**Option B: Add Warning/Description**

```php
class RoleResource extends Resource
{
    protected static ?string $navigationLabel = 'Roles (Advanced - do not use)';
    
    protected static ?string $modelLabel = 'Generic Role';
    
    protected static ?string $pluralModelLabel = 'Generic Roles';
    
    // Add navigation badge to warn
    protected static ?string $navigationBadge = '⚠️ Internal Use Only';
}
```

**Option C: Completely Disable**

```php
class RoleResource extends Resource
{
    protected static bool $isDiscovered = false;  // Don't auto-register
}
```

**Recommendation:** Go with **Option A** (hide but keep accessible)

**Estimated:** 10 menit

---

### ✅ Task 1.3: Add Relationship Diagram to Dashboard

**File:** Create new or edit `app/Filament/Panel/Pages/Dashboard.php`

**Add this section:**

```php
use Filament\Pages\Dashboard;

class Dashboard extends Dashboard
{
    // ... existing code ...

    public function getPanelStatistics(): array
    {
        return [
            // ... existing stats ...
        ];
    }

    // Add to page body:
    protected function getColumns(): int | string | array
    {
        return [
            'md' => 3,
            'lg' => 3,
        ];
    }
    
    // Add widget showing structure
}
```

**Or create a separate info page:**

```php
// app/Filament/Panel/Pages/IamStructure.php

namespace App\Filament\Panel\Pages;

use Filament\Pages\Page;

class IamStructure extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationLabel = 'IAM Structure Guide';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Help';
    
    protected static string $view = 'filament.pages.iam-structure';
}
```

Then create view: `resources/views/filament/pages/iam-structure.blade.php`

```blade
<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">IAM Role Assignment Structure</h1>
    
    <div class="bg-blue-50 p-4 rounded mb-4 border-l-4 border-blue-500">
        <h2 class="font-semibold text-lg mb-2">🎯 Recommended Flow:</h2>
        <p class="text-gray-700">
            <strong>User → Access Profile → Application Roles</strong>
        </p>
        <p class="text-sm text-gray-600 mt-2">
            An Access Profile bundles multiple roles across different applications,
            making it easy to assign related roles to users in one action.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- User Box -->
        <div class="border rounded-lg p-4 bg-gradient-to-br from-purple-50 to-purple-100">
            <h3 class="font-bold text-purple-900">👤 User</h3>
            <p class="text-sm text-purple-800 mt-2">
                John Doe<br>
                john@company.com
            </p>
        </div>

        <!-- Access Profile Box -->
        <div class="border rounded-lg p-4 bg-gradient-to-br from-blue-50 to-blue-100">
            <h3 class="font-bold text-blue-900">📦 Access Profile</h3>
            <p class="text-sm text-blue-800 mt-2">
                Quality Team<br>
                <span class="text-xs">4 bundled roles</span>
            </p>
        </div>

        <!-- Roles Box -->
        <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-green-100">
            <h3 class="font-bold text-green-900">🔐 Application Roles</h3>
            <p class="text-sm text-green-800 mt-2">
                SIIMUT: Lead<br>
                INCIDENT: Reporter
            </p>
        </div>
    </div>

    {{-- Add more helpful content --}}
</div>
```

**Estimated:** 30 menit

---

### ✅ Task 1.4: Standardize Language (Choose & Execute)

**Decision: English or Indonesian?**

Recommendation: **ENGLISH** (international, professional)

**Files to review:**

```
grep -r "→ (Indonesian labels)" app/Filament/Panel/Resources/
```

Main files:
1. `AccessProfileForm.php` - Has Indonesian descriptions ✅ (will update)
2. `UsersTable.php` - Mix of Indonesian headers + descriptions
3. All form labels
4. All table descriptions
5. All helper text

**Search & Replace needed:**

```
"Manajemen Pengguna"           → "User Management"
"Identitas profil akses..."    → "Access Profile Identity"
"Aplikasi"                     → "Applications"
"Roles & Permissions"          → Keep (already English)
"Ringkasan IAM"                → "IAM Summary" or "Access Summary"
"Pengguna"                     → "Users"
```

**Estimated:** 20-25 menit (with search/replace)

---

## 🎯 PHASE 2: MEDIUM EFFORT (1-2 hari kerja)

### Task 2.1: Improve Form Section Descriptions

**Files:**
- `AccessProfileForm.php`
- `ApplicationForm.php`
- `UserForm.php`

**Current AccessProfileForm:**

```php
Section::make('Profile Identity')
    ->description('Identitas profil akses...')  // ← OK tapi bisa lebih jelas
    
Section::make('Roles & Permissions')
    ->description('Mapping profile ini ke role-role aplikasi...')  // ← OK
```

**Improvement:**

```php
Section::make('Profile Identity')
    ->description(
        'Basic information that uniquely identifies this access profile. '
        . 'The slug is used internally by the system and cannot contain spaces.'
    )
    ->collapsible()
    ->collapsed(false)

Section::make('Assigned Roles')
    ->description(
        'Select which application roles this profile includes. '
        . 'Users assigned to this profile will automatically receive all selected roles. '
        . '<strong>Tip:</strong> Group roles by job function or department for easier management.'
    )
    ->collapsible()
    ->collapsed(false)

Section::make('Settings')
    ->description('Control when this profile can be used.')
    ->columnSpanFull()
    ->schema([
        Toggle::make('is_active')
            ->label('Active')
            ->helperText('Disable to prevent new user assignments while keeping existing assignments.'),
        Toggle::make('is_system')
            ->label('System Profile')
            ->helperText('System profiles are protected from deletion. This should only be enabled for critical, built-in profiles.'),
    ])
```

**Estimated:** 20 menit per form × 3 = 1 jam

---

### Task 2.2: Improve Table Column Descriptions & Tooltips

**File:** `UsersTable.php`

**Current:**

```php
TextColumn::make('accessible_apps')
    ->label('Aplikasi')  // ← Indonesian
    ->tooltip('Daftar aplikasi yang dapat diakses...')
```

**Improved:**

```php
TextColumn::make('accessible_apps')
    ->label('Accessible Applications')
    ->tooltip('Applications this user can access through assigned access profiles.')
    ->description('Showing apps across all assigned profiles')
```

**Add New Informative Columns:**

```php
// Show profile count
TextColumn::make('access_profiles_count')
    ->label('Assigned Profiles')
    ->counts('accessProfiles')
    ->badge()
    ->color('info')
    ->tooltip('Number of access profiles (role bundles) assigned to this user.'),

// Show total role count (calculated)
TextColumn::make('total_roles')
    ->label('Total Roles')
    ->getStateUsing(fn(User $record) => $record->applicationRoles()->count())
    ->badge()
    ->color('success')
    ->tooltip('Total application role assignments across all profiles.'),

// Show last modified
TextColumn::make('updated_at')
    ->label('Last Modified')
    ->dateTime()
    ->sortable()
    ->tooltip('When this user\'s access was last changed.'),
```

**Estimated:** 45 menit

---

### Task 2.3: Add Inline Help & Info Messages

**File:** `Users/Pages/EditUser.php` or view blade

**Add info banner:**

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make()
                ->schema([
                    // Add info box
                    View::make('components.iam-info-banner')
                        ->columnSpanFull(),
                    
                    // Existing fields...
                ]),
        ]);
}
```

**Create:** `resources/views/components/iam-info-banner.blade.php`

```blade
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
        </svg>
        <div>
            <h3 class="font-semibold text-blue-900">How to Assign Roles</h3>
            <p class="text-sm text-blue-800 mt-1">
                To grant this user access to applications:
            </p>
            <ol class="text-sm text-blue-800 mt-2 ml-4 list-decimal">
                <li>Scroll to <strong>Access Profiles</strong> section</li>
                <li>Click <strong>Attach</strong> button</li>
                <li>Select one or more profiles</li>
                <li>Save - user immediately gains all roles in those profiles</li>
            </ol>
        </div>
    </div>
</div>
```

**Estimated:** 30 menit

---

## 💎 PHASE 3: LARGER REFACTOR (Next Sprint)

### Task 3.1: Create Admin Onboarding Guide

**File:** `docs/FILAMENT-ADMIN-GUIDE.md`

Content:
- How to create an Access Profile
- How to create an Application
- How to add roles to an Application
- How to assign profiles to users
- Common workflows
- Troubleshooting

**Estimated:** 2-3 jam

---

### Task 3.2: Consider Renaming "Access Profile"

**Options:**

1. **Keep "Access Profile"** (current)
   - ✅ Already used, no refactoring
   - ⚠️ Doesn't intuitively mean "bundle of roles"

2. **Rename to "Role Bundle"**
   - ✅ More intuitive
   - ❌ Requires DB migration + code changes

3. **Rename to "Job Role" or "Job Title"**
   - ✅ Business language friendly
   - ❌ May conflict if real job titles exist

4. **Rename to "Access Template" / "Role Template"**
   - ✅ Clear what it does
   - ❌ Template might suggest it's read-only

**Recommendation:** Keep "Access Profile" but improve UX/docs to explain it better.

**If you decide to rename:**

```sql
-- Migration required
RENAME TABLE access_profiles TO role_bundles;
RENAME TABLE user_access_profiles TO user_role_bundles;
```

**Code changes:**
- Model name change
- Relationship names
- All references in forms/tables
- Database migrations

**Estimated:** 4-6 hari full

---

### Task 3.3: Create Admin Dashboard Widget

**File:** `app/Filament/Widgets/IamStructureWidget.php`

```php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Domain\Iam\Models\AccessProfile;
use App\Domain\Iam\Models\Application;
use Illuminate\Support\Collection;

class IamStructureWidget extends Widget
{
    protected static string $view = 'filament.widgets.iam-structure-widget';

    public function getStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_applications' => Application::count(),
            'total_profiles' => AccessProfile::count(),
            'active_profiles' => AccessProfile::where('is_active', true)->count(),
            'system_roles_count' => ApplicationRole::where('is_system', true)->count(),
        ];
    }

    public function getRecentChanges(): Collection
    {
        return User::latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'user' => $u->name,
                'action' => 'Updated',
                'date' => $u->updated_at->diffForHumans(),
            ]);
    }
}
```

**Estimated:** 2-3 jam

---

## ✅ QUICK CHECKLIST & PRIORITY

```
PHASE 1: QUICK WINS
═══════════════════════════════════════════════════════
⬜ [ ] 1.1 Standardize navigation group name
⬜ [ ] 1.2 Hide/mark Spatie Roles resource  
⬜ [ ] 1.3 Add IAM Structure info page
⬜ [ ] 1.4 Standardize language (English or Indonesian)
  Estimated: 1-2 hours

PHASE 2: MEDIUM EFFORT
═══════════════════════════════════════════════════════
⬜ [ ] 2.1 Improve form section descriptions
⬜ [ ] 2.2 Enhance table columns & tooltips
⬜ [ ] 2.3 Add inline help & info messages
  Estimated: 2-3 hours

PHASE 3: LARGER REFACTOR  
═══════════════════════════════════════════════════════
⬜ [ ] 3.1 Create comprehensive admin guide
⬜ [ ] 3.2 Evaluate "Access Profile" rename
⬜ [ ] 3.3 Build admin dashboard widgets
  Estimated: 1-2 weeks (depends on decision)
```

---

## 📂 FILES AFFECTED

### Phase 1 Files:
```
app/Filament/Panel/Resources/
├── AccessProfiles/AccessProfileResource.php
├── Applications/ApplicationResource.php
├── Users/UserResource.php
├── Roles/RoleResource.php
└── Pages/
    └── (new) IamStructure.php
    
resources/views/
└── (new) filament/pages/iam-structure.blade.php
```

### Phase 2 Files:
```
app/Filament/Panel/Resources/
├── AccessProfiles/Schemas/AccessProfileForm.php
├── Applications/Schemas/ApplicationForm.php
├── Users/
│   ├── Schemas/UserForm.php
│   └── Tables/UsersTable.php
├── (new) Widgets/IamStructureWidget.php
└── Pages/Users/EditUser.php

resources/views/
├── components/iam-info-banner.blade.php
└── (new) filament/widgets/iam-structure-widget.blade.php
```

### Documentation:
```
docs/
├── FILAMENT-UX-ANALYSIS.md ✅
├── FILAMENT-RESOURCE-HIERARCHY.md ✅
├── FILAMENT-IMPLEMENTATION-CHECKLIST.md ← (this file)
├── (new) FILAMENT-ADMIN-GUIDE.md
└── (new) FILAMENT-NAMING-CONVENTION.md
```

---

## 🎓 DECISION REQUIRED

**Before implementing, please decide:**

1. **Navigation Group Name:**
   - [ ] "IAM Management" (current, keep)
   - [ ] "Identity & Access" (modern)
   - [ ] "Administration" (broader)
   - [ ] Other: _______________

2. **Language Standard:**
   - [ ] English (recommended)
   - [ ] Indonesian
   - [ ] Bilingual (not recommended)

3. **Spatie Roles:**
   - [ ] Hide from navigation
   - [ ] Add warning label
   - [ ] Remove entirely

4. **Phase Timeline:**
   - [ ] Do Phase 1 this week
   - [ ] Do Phase 1 + 2 this sprint
   - [ ] Phase all gradually

---

**Document Status:** Complete Implementation Guide  
**Next Step:** Get approval for decisions above, then execute Phase 1
