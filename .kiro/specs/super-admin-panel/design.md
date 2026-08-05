# Design Document — Super Admin Panel

## Overview

إضافة نظام **Super Admin** منفصل تماماً عن الـ tenants.
الـ Super Admin بيدخل من `/admin/login`، عنده guard خاص، وبيدير الـ tenants من panel مستقل.
كمان يقدر يتنقل لـ dashboard أي tenant بـ impersonation.

---

## Architecture

```
/admin/*  routes
    │
    ├── EnsureSuperAdminAuthenticated middleware (admin guard)
    │
    ├── AdminLoginController
    ├── AdminDashboardController   ← list tenants
    ├── AdminTenantController      ← CRUD tenants + run migrations
    ├── AdminRoleController        ← manage roles/permissions per tenant
    ├── AdminUserController        ← manage users per tenant
    └── AdminImpersonateController ← enter/exit tenant
```

```
Central DB
├── admins table  ← Super Admin credentials (منفصل عن users)
└── users table   ← tenant users (مفيش تغيير)

Tenant DB
├── roles / permissions / model_has_roles / ... (Spatie)
└── ... (باقي جداول الـ tenant)
```

**الـ Guard:**
- `web` — للـ tenant users (موجود)
- `admin` — جديد للـ Super Admins، بيستخدم `admins` table في central DB

---

## Components and Interfaces

### 1. Admin Model (`app/Models/Admin.php`)
- `$connection = 'central'`
- `$guard_name = 'admin'`
- بيـ extend `Authenticatable`
- fields: `id`, `name`, `email`, `password`

### 2. Admin Guard (`config/auth.php`)
```php
'guards' => [
    'admin' => [
        'driver'   => 'session',
        'provider' => 'admins',
    ],
],
'providers' => [
    'admins' => [
        'driver' => 'eloquent',
        'model'  => App\Models\Admin::class,
    ],
],
```

### 3. Middleware (`app/Http/Middleware/EnsureSuperAdminAuthenticated.php`)
- بيتحقق إن `Auth::guard('admin')->check()` = true
- لو لأ → redirect `/admin/login`

### 4. Controllers
| Controller | المسئولية |
|---|---|
| `AdminLoginController` | login / logout للـ admin guard |
| `AdminDashboardController` | عرض قائمة الـ tenants |
| `AdminTenantController` | create / delete tenants + auto-migrate |
| `AdminRoleController` | CRUD roles وpermissions في tenant DB |
| `AdminUserController` | CRUD users مرتبطين بـ tenant |
| `AdminImpersonateController` | enter / exit tenant dashboard |

### 5. AdminTenantService (`app/Services/AdminTenantService.php`)
- `createTenant(name, domain)` — ينشئ tenant + يشغّل `tenants:migrate` تلقائياً
- `deleteTenant(tenant)` — يحذف tenant + users + يعمل `Artisan::call('tenants:run', ...)`

### 6. Routes (`routes/admin.php` أو section في `web.php`)
```
GET  /admin/login         → AdminLoginController@showForm
POST /admin/login         → AdminLoginController@login
POST /admin/logout        → AdminLoginController@logout

GET  /admin/dashboard     → AdminDashboardController@index  [middleware: auth:admin]

GET  /admin/tenants/create        → AdminTenantController@create
POST /admin/tenants               → AdminTenantController@store
DELETE /admin/tenants/{tenant}    → AdminTenantController@destroy

GET  /admin/tenants/{tenant}/roles         → AdminRoleController@index
POST /admin/tenants/{tenant}/roles         → AdminRoleController@store
DELETE /admin/tenants/{tenant}/roles/{role} → AdminRoleController@destroy
POST /admin/tenants/{tenant}/roles/{role}/permissions → AdminRoleController@syncPermissions

GET  /admin/tenants/{tenant}/users         → AdminUserController@index
POST /admin/tenants/{tenant}/users         → AdminUserController@store
DELETE /admin/tenants/{tenant}/users/{user} → AdminUserController@destroy

POST /admin/impersonate/{tenant}   → AdminImpersonateController@enter
GET  /admin/impersonate/exit       → AdminImpersonateController@exit
```

---

## Data Models

### `admins` table (central DB)
| column | type | notes |
|---|---|---|
| id | bigint PK | |
| name | string | |
| email | string unique | |
| password | string hashed | |
| remember_token | string nullable | |
| timestamps | | |

### Impersonation Session Keys
- `impersonating_tenant_id` — الـ tenant ID اللي الـ admin داخل عليه
- `admin_id` — عشان نرجع بعد الـ exit

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do.
Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

---

**Property 1: Admin authentication rejects invalid credentials**
*For any* email/password combination that does not match an admin record, THE System SHALL return an unauthenticated response and SHALL NOT create an admin session.
**Validates: Requirements 1.2, 1.3**

---

**Property 2: Admin routes are inaccessible without admin authentication**
*For any* `/admin/*` route, a request without a valid `admin` guard session SHALL receive a redirect to `/admin/login`.
**Validates: Requirements 1.4, 2.2**

---

**Property 3: Admin logout isolates session**
*For any* active admin session, calling logout SHALL invalidate the admin guard session and SHALL NOT affect the `web` guard session (tenant user session).
**Validates: Requirements 1.5**

---

**Property 4: Tenant list contains all tenants**
*For any* set of tenants in the central DB, the admin dashboard response SHALL contain each tenant's name, domain, and created_at.
**Validates: Requirements 2.1**

---

**Property 5: Tenant creation stores record and runs migrations**
*For any* valid (name, domain) pair, after calling `createTenant` the tenant SHALL exist in the central DB and the tenant database SHALL contain the standard schema tables.
**Validates: Requirements 3.1**

---

**Property 6: Duplicate domain is rejected**
*For any* domain that already exists in the tenants table, a creation attempt SHALL fail with a validation error and SHALL NOT create a new tenant record.
**Validates: Requirements 3.2**

---

**Property 7: Role isolation between tenants**
*For any* role created for tenant A, that role SHALL exist in tenant A's database and SHALL NOT exist in tenant B's database.
**Validates: Requirements 4.2**

---

**Property 8: Permission sync updates correct tenant DB**
*For any* role-permission assignment for a given tenant, the `role_has_permissions` pivot in that tenant's DB SHALL reflect the assigned permissions, and other tenant DBs SHALL be unaffected.
**Validates: Requirements 4.3**

---

**Property 9: Role deletion removes role and permissions**
*For any* role in a tenant DB, after deletion the role SHALL NOT exist and all its `role_has_permissions` records SHALL be removed from that tenant's DB.
**Validates: Requirements 4.4**

---

**Property 10: User list is scoped to tenant**
*For any* tenant, the users list returned by the admin panel SHALL contain exactly the users in the central DB whose `tenant_id` equals that tenant's ID.
**Validates: Requirements 5.1**

---

**Property 11: User creation assigns correct tenant_id and role**
*For any* valid user creation for tenant T with role R, the user SHALL be stored in central DB with `tenant_id = T.id`, and the role R SHALL be assigned in T's tenant DB.
**Validates: Requirements 5.2**

---

**Property 12: Duplicate email is rejected**
*For any* email that already exists in the central DB users table, a user creation attempt SHALL fail with a validation error.
**Validates: Requirements 5.4**

---

**Property 13: Impersonation enter/exit round-trip**
*For any* tenant, entering impersonation then exiting SHALL restore the admin session state to what it was before entering, with `impersonating_tenant_id` cleared.
**Validates: Requirements 6.3**

---

**Property 14: Tenant deletion cascades to users**
*For any* tenant T, after deletion there SHALL be no users in the central DB with `tenant_id = T.id`.
**Validates: Requirements 7.1, 7.2**

---

## Error Handling

| الحالة | السلوك |
|---|---|
| Domain مكررة عند إنشاء tenant | Validation error — 422 |
| Email مكرر عند إنشاء user | Validation error — 422 |
| Tenant مش موجود عند impersonation | Error message + redirect to admin dashboard |
| `tenants:migrate` فشل | Log error + rollback tenant creation |
| حذف tenant عنده active sessions | Flush sessions من central DB sessions table |

---

## Testing Strategy

### Unit Tests
- `AdminTenantService::createTenant()` — verify tenant record + Artisan call
- `AdminTenantService::deleteTenant()` — verify tenant + users deleted
- Domain/email uniqueness validation
- Role isolation: role created in tenant A doesn't appear in tenant B

### Property-Based Testing
باستخدام **PestPHP** مع **Faker** لتوليد بيانات عشوائية.

كل property-based test لازم:
- يشغّل minimum 100 iteration
- يكون معلّق بـ comment بالـ format: `// Feature: super-admin-panel, Property {N}: {text}`
- يغطي property واحدة بالظبط من الـ Correctness Properties فوق

**Properties المستهدفة:**
- Property 2: Admin routes protection — generate random admin routes, verify all redirect without auth
- Property 6: Duplicate domain rejection — generate existing domains, verify all fail
- Property 7: Role isolation — create role for tenant A, verify absent in tenant B
- Property 10: User list scoping — generate users with mixed tenant_ids, verify correct filtering
- Property 12: Duplicate email rejection — generate existing emails, verify all fail
- Property 13: Impersonation round-trip — enter then exit, verify session state restored
- Property 14: Tenant deletion cascade — create tenant + users, delete, verify users gone
