# Filament Resource Hierarchy & UX Flow

## 🏗️ CURRENT RESOURCE STRUCTURE

```
FILAMENT PANEL
│
├─ Navigation Group: "IAM Management" (inconsistent)
│  │
│  ├─ Access Profiles (sort: 10)
│  │  ├─ List View (table)
│  │  ├─ Create Form
│  │  ├─ Edit Form
│  │  ├─ Relations:
│  │  │  ├─ Roles (RolesRelationManager)
│  │  │  └─ Users (UsersRelationManager)
│  │  │
│  │  └─ Models: AccessProfile (has many roles, has many users)
│  │
│  ├─ Applications (sort: 20)
│  │  ├─ List View (table)
│  │  ├─ Create Form
│  │  ├─ View Page
│  │  ├─ Edit Form
│  │  ├─ Relations:
│  │  │  └─ Roles (RolesRelationManager - ApplicationRole type)
│  │  │
│  │  └─ Models: Application (has many ApplicationRoles)
│  │
│  ├─ Users (sort: 30)
│  │  ├─ List View (table with summary info)
│  │  ├─ Create Form
│  │  ├─ View Page
│  │  ├─ Edit Form
│  │  ├─ Relations:
│  │  │  ├─ Roles (RolesRelationManager - SPATIE ROLE, minimal)
│  │  │  ├─ Access Profiles (AccessProfilesRelationManager) ← KEY
│  │  │  └─ Applications (ApplicationsRelationManager)
│  │  │
│  │  └─ Models: User (has many roles, has many accessProfiles)
│  │
│  └─ Roles (sort: 20) ← SPATIE ROLE RESOURCE
│     ├─ List View
│     ├─ Create Form
│     ├─ View Page
│     ├─ Edit Form
│     └─ Models: Spatie\Permission\Models\Role
│
└─ Navigation Group: "IAM" (some resources use this) ← INCONSISTENT
```

---

## 🔗 DATA MODEL RELATIONSHIPS

### **LAYER 1: User ↔ Access Profile**

```
┌──────────┐          ┌──────────────────┐
│  User    │◄────────►│  Access Profile  │
└──────────┘ M:M      └──────────────────┘
     │                        │
     │ nip                     │ slug
     │ name                    │ name
     │ email                   │ description
     │                         │ is_system
     │                         │ is_active
     │
     │                         ▼
     │                ┌──────────────────┐
     │                │ ApplicationRole  │
     │                └──────────────────┘
     │                    M:M from profile
     │                     │ slug
     │                     │ name
     │                     │ app_id
     │
     │  (ALSO DIRECT)   ▼
     │
     └──────────────────────►┌──────────────┐
                            │AppRole       │
                            └──────────────┘
                         (LEGACY, NOT RECOMMENDED)
     
     (ALSO SPATIE ROLE)
     └──────────────────────►┌──────────────┐
                            │Spatie Role   │
                            └──────────────┘
                         (GENERIC, NOT RECOMMENDED)
```

### **LAYER 2: Application ↔ ApplicationRole**

```
┌─────────────┐   1:N   ┌──────────────────┐
│ Application │◄────────│ ApplicationRole  │
└─────────────┘         └──────────────────┘
      │                        │
      │ id                      │ id
      │ app_key                 │ slug
      │ name                    │ name
      │ description             │ description
      │ enabled                 │ is_system
      │ secret                  │ application_id
      │ logo_url                │
      │ token_expiry            │
      │                         │
      │                         ▼
      │                    ┌──────────┐
      │                    │ User     │
      │      (INDIRECT)    └──────────┘
      │      via AppRole       (M:M)
      │
      └────────────────────────────────►┌──────────────┐
                      (DIRECT - LEGACY) │ User         │
                                        └──────────────┘
```

### **THE RECOMMENDED FLOW (Highlighted)**

```
┌─────────────────────────────────────────────────────┐
│  RECOMMENDED: User → Access Profile → App Roles    │
├─────────────────────────────────────────────────────┤
│                                                      │
│  User "John Doe"                                    │
│    └─ Access Profile "Quality Team"                │
│       ├─ App: SIIMUT → Role: Quality Lead          │
│       ├─ App: SIIMUT → Role: Report Viewer         │
│       ├─ App: INCIDENT → Role: Reporter            │
│       └─ App: DATABASE → Role: Auditor             │
│                                                      │
│  ✅ SIMPLE: Assign 1 profile = 4 roles instantly   │
│  ✅ SCALABLE: Update profile = all users updated   │
│  ✅ MANAGEABLE: One place to manage role groups    │
│                                                      │
└─────────────────────────────────────────────────────┘
```

---

## 📊 FILAMENT PAGES OVERVIEW

### **AccessProfileResource Pages**

| Page | Purpose | Model Binding | Key Features |
|------|---------|---------------|--------------|
| `ListAccessProfiles` | View all profiles | - | Sort, search, filter, create |
| `CreateAccessProfile` | New profile | - | Basic form, auto slug |
| `EditAccessProfile` | Update profile | Profile ID | Edit name, description, roles |
| (relation) | View profile users | - | Relation manager |
| (relation) | Manage profile roles | - | Attach/detach ApplicationRoles |

### **ApplicationResource Pages**

| Page | Purpose | Model Binding | Key Features |
|------|---------|---------------|--------------|
| `ListApplications` | View all apps | - | Show status, roles count, enabled |
| `CreateApplication` | New app | - | App key, secret, URLs |
| `EditApplication` | Update app | App ID | Edit settings (not secret) |
| `ViewApplication` | App details | App ID | InfoList with metadata |
| (relation) | Manage app roles | - | Create/edit/delete ApplicationRoles |

### **UserResource Pages**

| Page | Purpose | Model Binding | Key Features |
|------|---------|---------------|--------------|
| `ListUsers` | View all users | - | Show accessible apps, profile count |
| `CreateUser` | New user | - | NIP, name, email, active flag |
| `EditUser` | Update user | User ID | Edit basic info |
| `ViewUser` | User details | User ID | InfoList with profile summary |
| (relation) | User roles (Spatie) | - | ❌ Minimal, mostly empty |
| (relation) | User profiles | - | ✅ Attach/detach AccessProfiles |
| (relation) | User apps | - | ✅ Show accessible apps summary |

---

## 🎯 UX FLOW DIAGRAM

### **HAPPY PATH: Assign Role to User**

```
┌─────────────────────────────────────────────────┐
│ Scenario: Assign "Quality Team" profile to user │
└─────────────────────────────────────────────────┘

START
 │
 ├─► OPTION A: Start from USER
 │    └─► User List
 │        └─► Click "John Doe" (Edit)
 │            └─► Scroll to "Access Profiles" tab
 │                └─► Click "Attach" button
 │                    └─► Select "Quality Team"
 │                        └─► Confirm
 │                            └─ John has 4 roles ✅
 │
 ├─► OPTION B: Start from ACCESS PROFILE
 │    └─► Access Profile List
 │        └─► Click "Quality Team"
 │            └─► Scroll to "Users" tab
 │                └─► Click "Attach" button
 │                    └─► Select "John Doe"
 │                        └─► Confirm
 │                            └─ John has 4 roles ✅
 │
 └─► OPTION C (UNCLEAR - DON'T USE)
      └─► User Detail
          └─► "Roles" tab (Spatie - minimal)
              └─► ??? (not really functional)
```

### **PROBLEM ZONES**

```
┌─────────────────────────────────────────────┐
│ Zone 1: User → "Roles" tab (Spatie)         │
├─────────────────────────────────────────────┤
│ ❌ Misleading - suggests you can manage roles│
│ ❌ Actually empty/non-functional             │
│ ❌ User confused: "Where do I assign roles?" │
│ 🔧 FIX: Hide this tab or repurpose it       │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ Zone 2: Resource "Roles" (Spatie)           │
├─────────────────────────────────────────────┤
│ ❌ Navigation shows "Roles" separately       │
│ ❌ User wonders: Use this or "Access Prof"? │
│ ❌ Different role system (not app-aware)    │
│ 🔧 FIX: Explain clearly or hide from menu   │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ Zone 3: Application → Roles (Relation Mgr)  │
├─────────────────────────────────────────────┤
│ ⚠️ User can create roles here, but          │
│ ❌ No UI explanation of relationship         │
│ ⚠️ Unclear if this is "ApplicationRole"     │
│ 🔧 FIX: Add section title & description    │
└─────────────────────────────────────────────┘
```

---

## 🏷️ NAMING CONFUSION MATRIX

| What User Might Search For | Actual Name in System | Where It Is | Problem |
|---------------------------|----------------------|-------------|---------|
| "Manage user roles" | Access Profiles / ApplicationRoles | 3 places | Too many options |
| "Find role in app" | ApplicationRole (in relation mgr) | Application Resource | Hidden in relation mgr |
| "Assign role to user" | Access Profile attach | User Resource | Correct, but unclear |
| "Define new role" | ApplicationRole create | Application > Roles | Hidden, non-obvious |
| "Global roles" | Spatie Role | Separate Resource | Confusing - not for IAM |

---

## 🎨 VISUAL LABEL ANALYSIS

### **Current Labels (Inconsistent)**

```
NAVIGATION:
  ✅ "Access Profiles"
  ✅ "Applications"  
  ✅ "Users"
  ⚠️  "Roles" (Spatie, confusing)
  
DIRECTORY NAMES:
  ✅ AccessProfiles/     (singular class: AccessProfile)
  ✅ Applications/       (singular class: Application)
  ✅ Users/            (singular class: User)
  ✅ Roles/            (singular class: RoleResource)

BUTTON LABELS:
  ✨ "Create Access Profile"
  ✨ "Create Role" (inside Application)
  
RELATION MANAGER TITLES:
  ✅ "Assigned Application Roles" (AccessProfile > Roles)
  ✅ "Application Roles" (Application > Roles)
  ❓ "Access Profiles" (User > AccessProfiles)
  ❓ (missing) (User > Roles Spatie)
  ❓ (missing) (AccessProfile > Users)

FORM SECTIONS (Indonesian):
  ⚠️ "Profile Identity"
  ⚠️ "Roles & Permissions"
  ⚠️ "Metadata & Description"
  
  vs
  
  ⚠️ "Identitas profil akses..." (some are Indonesian)
```

---

## 📱 USE CASE SCENARIOS

### **Scenario 1: Onboarding New User**

**Current Flow:**
1. Go to Users
2. Create User
3. Fill NIP, email, name
4. Save
5. Go back to User → Edit
6. Click "Access Profiles" tab
7. Click "Attach"
8. Search & select "Quality Team"
9. Done

**Pain Points:**
- ❌ Multi-step process
- ❌ Need to edit after create
- ❌ "Roles" tab is empty (confusing)

---

### **Scenario 2: Update Role Permissions**

**Current Flow:**
1. Go to Applications
2. Select "SIIMUT"
3. In "Application Roles" tab, find role
4. Click Edit (inline modal)
5. Update permissions
6. All users with this role (via profile) get updated ✓

**What's Good:**
- ✅ Permissions update automatically
- ✅ No stale role definitions

**What's Unclear:**
- ❌ User might not realize all affected users get the new permissions
- ❌ No audit trail shown

---

### **Scenario 3: Create New Access Profile**

**Current Flow:**
1. Go to Access Profiles
2. Create new
3. Fill Name (slug auto-generates) ✓
4. Fill Description
5. In "Roles & Permissions" section, select roles
6. Save
7. Users can now be assigned this profile

**What's Good:**
- ✅ Slug auto generation
- ✅ Clear section descriptions in Indonesian

**What Could Be Better:**
- ⚠️ Multi-language mix (section title in English, description in Indonesian)
- ❓ No preview of what roles user will get
- ❓ No way to test/preview profile

---

## ✅ RECOMMENDATIONS SUMMARY

### **QUICK WINS (Do Now)**

1. **Hide Spatie Roles tab from User Resource**
   - Or clearly label it as "Generic Roles (Advanced)"
   - Add disclaimer note

2. **Standardize Navigation Group**
   - Pick one: "IAM Management" or "Identity & Access"
   - Use everywhere

3. **Standardize Language**
   - Choose: English OR Indonesian (not mix)
   - Update all labels, descriptions, tooltips

### **MEDIUM EFFORT (Do This Sprint)**

4. **Add Relationship Visualization**
   - Create diagram on Dashboard
   - Or add help section on Users page

5. **Improve Form Descriptions**
   - Explain why each field matters
   - Show examples

6. **Better Column Descriptions**
   - In tables, show what data means
   - Use tooltips liberally

### **LARGER REFACTOR (Future)**

7. **Consider Renaming "Access Profile"**
   - More intuitive terms:
     - "Role Bundle"
     - "Job Role" / "Job Title"
     - "Role Group"
     - "Access Profile" is fine too, just needs better UX

8. **Consolidate Role Management**
   - If Spatie Roles truly not needed, remove
   - If needed, clearly separate concerns

---

## 📚 DOCUMENTATION NEEDED

Create these docs in `/docs/`:

```
docs/
├─ FILAMENT-UX-ANALYSIS.md (✅ Done)
├─ FILAMENT-NAMING-CONVENTION.md (TODO)
├─ RESOURCE-HIERARCHY-GUIDE.md (TODO)
├─ USER-ROLE-ASSIGNMENT-GUIDE.md (TODO)
└─ ADMIN-ONBOARDING.md (TODO)
```

---

**Document Status:** Complete Visualization & Analysis
**Author:** Code Analysis  
**Date:** 10 Feb 2026
