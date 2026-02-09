# Analisis UX & Naming Convention - Filament IAM Panel

**Tanggal Analisis:** 10 Februari 2026  
**Fokus:** Resource Naming & User Experience Clarity

---

## 📋 Executive Summary

Sistem IAM memiliki **3 resource utama** di Filament:
1. **Users** - Pengguna sistem
2. **Applications** - Aplikasi/sistem yang terintegrasi
3. **Access Profiles** - Pengelompokan role lintas aplikasi

**Masalah utama yang ditemukan:**
- ❌ Terminologi/naming yang tidak konsisten (AccessProfile vs Access Profile vs Profile)
- ❌ Dua sistem role yang berbeda (ApplicationRole vs Spatie Role) menyebabkan kebingungan
- ❌ Navigasi & label mencampur Bahasa Indonesia dan Inggris tanpa konsistensi
- ❌ Hubungan User → AccessProfile → ApplicationRole tidak jelas di UI
- ⚠️ Form sections yang redundan atau kurang deskriptif

---

## 🔍 Analisis Detail

### 1. STRUKTUR RESOURCE & NAMING

#### **Resource Architecture (Current)**
```
Filament/Panel/Resources/
├── AccessProfiles/        ✅ Plural form (good)
│   ├── AccessProfileResource.php
│   ├── Pages/ (List, Create, Edit)
│   ├── RelationManagers/ (Roles, Users)
│   ├── Schemas/ (Form, Infolist)
│   └── Tables/
├── Applications/          ✅ Plural form (good)
│   ├── ApplicationResource.php
│   ├── Pages/
│   ├── RelationManagers/ (Roles)
│   ├── Schemas/
│   └── Tables/
└── Users/                 ❌ Plural but inconsistent with singular model
    ├── UserResource.php
    ├── Pages/
    ├── RelationManagers/ (AccessProfiles, Roles, Applications)
    ├── Schemas/
    └── Tables/
```

#### **Naming Inconsistencies**

| Aspek | Current | Issue |
|-------|---------|-------|
| **Directory** | `AccessProfiles/` | Plural ✅ |
| **Class Name** | `AccessProfileResource` | Singular ✅ |
| **Navigation Label** | `Access Profiles` | Konsisten ✅ |
| **Model Label** | `Access Profile` | Konsisten ✅ |
| **Plural Label** | `Access Profiles` | Konsisten ✅ |
| | | |
| **Directory** | `Applications/` | Plural ✅ |
| **Class Name** | `ApplicationResource` | Singular ✅ |
| **Navigation Label** | `Applications` | Konsisten ✅ |
| **Model Label** | `Application` | Konsisten ✅ |
| | | |
| **Directory** | `Users/` | Plural ✅ |
| **Class Name** | `UserResource` | Singular ✅ |
| **Navigation Label** | `Users` | Konsisten ✅ |
| **Model Label** | `User` | Konsisten ✅ |

✅ **Resource naming sendiri sudah konsisten!**

---

### 2. MASALAH UTAMA: DUAL ROLE SYSTEM 🚨

#### **Masalah Fundamental**

Project menggunakan **2 sistem role yang berbeda** namun saling terhubung:

```
┌─────────────────────────────────────────────────────────┐
│ SISTEM 1: Spatie Permission (Generic)                  │
├─────────────────────────────────────────────────────────┤
│ • Model: Spatie\Permission\Models\Role                  │
│ • Scope: Global (tidak application-aware)               │
│ • Filament Resource: app/Filament/Panel/Resources/Roles │
│ • Digunakan untuk: RBAC umum Filament                   │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ SISTEM 2: IAM ApplicationRole (Multi-Tenant)            │
├─────────────────────────────────────────────────────────┤
│ • Model: App\Domain\Iam\Models\ApplicationRole           │
│ • Scope: Per-Application (application-aware)            │
│ • Filament: RELATION MANAGERS only (tidak ada Resource) │
│ • Digunakan untuk: IAM OAuth/SSO multi-aplikasi         │
│ • Table: iam_roles (bukan roles)                        │
└─────────────────────────────────────────────────────────┘
```

#### **Di Filament:**

```php
// AccessProfileResource - manage roles via relation manager
public function getRelations(): array
{
    return [
        RelationManagers\RolesRelationManager::class,  // ← ApplicationRole
        RelationManagers\UsersRelationManager::class,
    ];
}

// UserResource - manage MULTIPLE roles
public function getRelations(): array
{
    return [
        RolesRelationManager::class,           // ← Spatie Role (generic, minimal)
        ApplicationsRelationManager::class,     // ← Applications accessible
        AccessProfilesRelationManager::class,  // ← AccessProfile (grouped roles)
    ];
}

// ApplicationResource - manage roles via relation manager
public function getRelations(): array
{
    return [
        RelationManagers\RolesRelationManager::class,  // ← ApplicationRole
    ];
}
```

#### **Kebingungan User:**

1. User melihat **2 tempat berbeda** untuk manage role:
   - Tab "Roles" (Spatie) - kosong/minimal
   - Tab "Access Profiles" - kumpulan role aplikasi
   - Yang "real" adalah Access Profiles / ApplicationRoles

2. Terminologi membingungkan:
   - "Roles" (Spatie) vs 
   - "Application Roles" (IAM) vs 
   - "Access Profiles" (grouping mechanism)

3. Di `ApplicationResource`, manage roles tapi tidak ada Resource untuk ApplicationRole sendiri

---

### 3. STRUKTUR HIERARKI & CLARITY

#### **Current User Assignment Flow**

```
User
  ├─ Via Access Profiles (RECOMMENDED)
  │  └─ Access Profile = collection of ApplicationRoles
  │     └─ ApplicationRole = role per aplikasi
  │
  ├─ Via Direct ApplicationRoles (LEGACY?)
  │  └─ ApplicationRole = role per aplikasi
  │
  └─ Via Spatie Roles (GENERIC)
     └─ Spatie Role = global role (not recommended for IAM)
```

**Problem:** Tiga cara berbeda untuk assign roles, user tidak tahu mana yang benar!

#### **Current "Access Profile" Concept (Good)**

Ini adalah konsep terbaik - **mengelompokkan role lintas aplikasi:**

```
Access Profile "Quality Team"
  ├─ App: SIIMUT → Role: Quality Lead
  ├─ App: SIIMUT → Role: Report Viewer
  ├─ App: INCIDENT → Role: Reporter
  └─ App: DATABASE → Role: Auditor

User "John Doe"
  └─ Assign Access Profile "Quality Team"
     └─ Automatically gets all 4 roles across 4 apps
```

✅ **Ini approach yang benar, hanya perlu clarity di UI**

---

### 4. FILAMENT NAVIGATION & LABELS

#### **Current Navigation Structure**

```
Navigation Group: "IAM Management" (inconsistent, ada yang "IAM")
  ├─ ✅ Access Profiles (sort: 10)
  ├─ ✅ Applications (sort: 20) 
  ├─ ✅ Users (sort: 30)
  ├─ ❓ Roles (sort: 20) - DUPLICATE, confusing
  └─ ❓ Users (sort: 30) - DUPLICATE
```

**Masalah:**
1. Navigation Group inconsistent ("IAM Management" vs "IAM")
2. Ada Roles Resource yang separate - user bingung mana yang pakai
3. User tidak tahu hubungan antara Access Profile → Role → Application

---

### 5. FORM LABELS & DESCRIPTIONS

#### **AccessProfileForm - Good Description** ✅

```php
Section::make('Profile Identity')
    ->description('Identitas profil akses yang digunakan untuk mengelompokkan hak akses lintas aplikasi.')
    // ✅ Jelas tujuannya

Section::make('Roles & Permissions')
    ->description('Mapping profile ini ke role-role aplikasi. Satu profile bisa punya banyak role lintas aplikasi.')
    // ✅ Jelas hubungannya
```

#### **UsersTable - Mix Bahasa** ⚠️

```php
->heading('Manajemen Pengguna')  // ← Indonesian
->description('Kelola akun IAM, hak akses aplikasi, dan status keamanan pengguna.')  // ← Indonesian

TextColumn::make('accessible_apps')
    ->label('Aplikasi')  // ← Indonesian
    ->tooltip('Daftar aplikasi yang dapat diakses pengguna melalui IAM.')  // ← Indonesian
```

**Issue:** Sebagian Inggris, sebagian Indonesian - tidak konsisten

---

### 6. RELATION MANAGERS NAMING

#### **Yang Ada:**

| Resource | RelationManager | Title | Issue |
|----------|-----------------|-------|-------|
| AccessProfile | `RolesRelationManager` | "Assigned Application Roles" | ✅ Clear |
| AccessProfile | `UsersRelationManager` | (default) | ❓ Missing explicit title |
| Application | `RolesRelationManager` | "Application Roles" | ✅ Clear |
| User | `RolesRelationManager` | (default, minimal) | ❌ Not implemented proper |
| User | `AccessProfilesRelationManager` | "Access Profiles" | ✅ Clear |
| User | `ApplicationsRelationManager` | (new, can infer) | ❓ Check if exists |

**Issue:** User RelationManager untuk Spatie Roles hampir kosong (TODO comment ditemukan)

```php
// app/Filament/Panel/Resources/Users/RelationManagers/RolesRelationManager.php
class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // TODO: Hubungkan ke Spatie teams (application) pada iterasi berikutnya.
                TextColumn::make('name')
                    ->label('Role')
                    ->searchable(),
            ])
            // ... (minimal implementation)
    }
}
```

---

### 7. TABLE COLUMNS & INFO CLARITY

#### **UsersTable Column: "Ringkasan IAM"** ⚠️

```php
TextColumn::make('iam_summary')
    ->label('Ringkasan IAM')  // ← Vague
    ->description('Daftar aplikasi yang dapat diakses pengguna melalui IAM.')
```

**Lebih baik:**
- "Access Summary" atau "Profile & App Count"
- Dengan icon yang lebih jelas

#### **ApplicationsTable** ✅

Sudah cukup clear:
- App Key (badge, distinct)
- Status (icon: check/x)
- Roles count (info badge)
- Callback URL (copyable)

---

## 🎯 KESIMPULAN: 5 MAIN ISSUES

| # | Issue | Severity | Impact |
|---|-------|----------|--------|
| 1️⃣ | **Dual Role System** (Spatie + ApplicationRole) | 🔴 HIGH | User confused, unclear flow |
| 2️⃣ | **Inconsistent Terminology** | 🟡 MEDIUM | "Role" vs "ApplicationRole" vs "Access Profile" |
| 3️⃣ | **Navigation Group Inconsistency** | 🟡 MEDIUM | "IAM" vs "IAM Management" |
| 4️⃣ | **Language Mixing** (Inggris + Indonesian) | 🟡 MEDIUM | Unprofessional, confusing |
| 5️⃣ | **Unclear User-Profile-Role Hierarchy** | 🟡 MEDIUM | Missing visual/textual flow |

---

## 💡 REKOMENDASI (PRIORITAS)

### Priority 1: Clarity (High Impact, Low Effort)

- [ ] **Standardize Language:** All Filament UI in English (atau semua Indonesian)
- [ ] **Standardize Navigation Group:** Gunakan konsisten "IAM" atau "IAM Management"
- [ ] **Hide/Remove Spatie Roles Tab:** Jika tidak digunakan, jangan tempatkan di User resource
- [ ] **Add Visual Flow Diagram:** Di dashboard atau documentation pane

### Priority 2: Terminology (Medium Impact, Medium Effort)

- [ ] **Create Naming Convention Doc:** Define singular/plural clearly
- [ ] **Update All Labels:** Use consistent terminology
- [ ] **Add Helper Text:** Explain relationship User → AccessProfile → ApplicationRole
- [ ] **Consider Rename:** "Access Profile" bisa jadi "Role Profile" atau "Role Bundle"

### Priority 3: UX Enhancement (Medium Impact, High Effort)

- [ ] **Add Dashboard/Overview:** Show relationship visualization
- [ ] **Improve Form Sections:** More descriptive, better layout
- [ ] **Create Tooltips:** Explain what each role grants
- [ ] **Add Inline Help:** In tables, show related data

---

## 📊 DETAIL UNTUK DIREKOMENDASI

### A. Naming Convention yang Diusulkan

```php
// JANGAN: Role (ambiguous), Spatie Role (internal)
// GUNAKAN: Application Role (jelas scope-nya)

// JANGAN: Access Profile, Profile (ambiguous)
// GUNAKAN: Role Profile / Role Bundle / Grouped Roles (clearer)

// JANGAN: Roles (generic)
// GUNAKAN: Application Roles (specific), Grouped Roles (bundled)
```

### B. Navigation yang Disarankan

```php
// KONSISTEN:
protected static string|UnitEnum|null $navigationGroup = 'IAM Management';

// ATAU:
protected static string|UnitEnum|null $navigationGroup = 'Identity & Access';

// JANGAN CAMPUR:
// ✗ Sebagian "IAM", sebagian "IAM Management"
```

### C. Language Standardization

**Option 1: All English (Recommended for Enterprise)**
```php
->heading('User Management')
->description('Manage user accounts, application access, and security status.')
->label('Accessible Applications')
->tooltip('List of applications accessible by this user through IAM.')
```

**Option 2: All Indonesian (Recommended for Local)**
```php
->heading('Manajemen Pengguna')
->description('Kelola akun pengguna, akses aplikasi, dan status keamanan.')
->label('Aplikasi yang Bisa Diakses')
->tooltip('Daftar aplikasi yang dapat diakses pengguna melalui IAM.')
```

**Current:** ❌ Mix both (worst option)

---

## 📈 IMPLEMENTASI ROADMAP

```
Phase 1: Documentation & Terminology
├─ Create NAMING-CONVENTION.md
├─ Create ROLE-HIERARCHY-GUIDE.md
└─ Decide: English vs Indonesian standard

Phase 2: Non-Breaking UI Changes
├─ Standardize navigation groups
├─ Update labels consistently
├─ Add tooltips & descriptions
└─ Hide unused Spatie Roles component

Phase 3: UX Enhancement
├─ Add relationship visualization
├─ Create onboarding guide
├─ Improve form layouts
└─ Add inline help text

Phase 4: Optional Refactoring
├─ Consider renaming "Access Profile"
├─ Consolidate role management
└─ Create shared terminology docs
```

---

## 📚 NEXT STEPS

1. **Review**: Approve naming convention pilihan
2. **Approve**: Language standardization
3. **Implement**: Priority 1 recommendations
4. **Test**: UX flow dengan user actual
5. **Document**: Create user guide berdasarkan new terminology

---

**Generated:** 10 Feb 2026 | **Status:** Analysis Complete, Awaiting Review
