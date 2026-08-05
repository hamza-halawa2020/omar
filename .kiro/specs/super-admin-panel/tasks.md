# Implementation Plan — Super Admin Panel

- [x] 1. Setup: migrations, model, and guard



  - Create `admins` table migration in `database/migrations/` (central DB)
  - Create `app/Models/Admin.php` — extends `Authenticatable`, `$connection = 'central'`
  - Add `admin` guard and `admins` provider to `config/auth.php`
  - _Requirements: 1.1, 1.2_

- [ ] 2. Authentication
- [x] 2.1 Create `EnsureSuperAdminAuthenticated` middleware


  - Checks `Auth::guard('admin')->check()`, redirects to `/admin/login` if false
  - Register alias `auth.admin` in `bootstrap/app.php`
  - _Requirements: 1.4, 2.2, 2.3_

- [ ]* 2.2 Write property test — Admin routes protection (Property 2)
  - `// Feature: super-admin-panel, Property 2: Admin routes are inaccessible without admin authentication`
  - **Validates: Requirements 1.4, 2.2**




- [x] 2.3 Create `AdminLoginController`

  - `showForm()` → returns `admin.auth.login` view
  - `login()` → Auth::guard('admin')->attempt(), redirect to `/admin/dashboard`
  - `logout()` → Auth::guard('admin')->logout(), session invalidate, redirect to `/admin/login`
  - _Requirements: 1.1, 1.2, 1.3, 1.5_

- [ ]* 2.4 Write property test — Authentication and session isolation (Properties 1, 3)
  - `// Feature: super-admin-panel, Property 1: Admin authentication rejects invalid credentials`

  - `// Feature: super-admin-panel, Property 3: Admin logout isolates session`
  - **Validates: Requirements 1.2, 1.3, 1.5**

- [-] 3. Routes and views

- [x] 3.1 Add admin routes in `routes/web.php` (or separate `routes/admin.php`)


  - Public: `GET /admin/login`, `POST /admin/login`
  - Protected (middleware `auth.admin`): all `/admin/*` routes
  - _Requirements: 1.1, 2.2_

- [x] 3.2 Create admin layout and login view


  - `resources/views/admin/layouts/app.blade.php`
  - `resources/views/admin/auth/login.blade.php`
  - _Requirements: 1.1_

- [ ] 4. Tenant management
- [x] 4.1 Create `AdminTenantService`


  - `createTenant(name, domain)` — creates tenant record + calls `Artisan::call('tenants:migrate', ['--tenants' => [$id]])`
  - `deleteTenant(tenant)` — deletes users with `tenant_id`, drops tenant DB, deletes tenant record
  - _Requirements: 3.1, 3.2, 7.1, 7.2_

- [ ]* 4.2 Write property test — Tenant creation and duplicate domain (Properties 5, 6)
  - `// Feature: super-admin-panel, Property 5: Tenant creation stores record and runs migrations`
  - `// Feature: super-admin-panel, Property 6: Duplicate domain is rejected`
  - **Validates: Requirements 3.1, 3.2**


- [x] 4.3 Create `AdminTenantController`

  - `index()` → list all tenants
  - `create()` → show form
  - `store()` → validate + call `AdminTenantService::createTenant()`
  - `destroy()` → call `AdminTenantService::deleteTenant()`
  - _Requirements: 2.1, 3.1, 3.2, 3.3, 7.1, 7.2_

- [ ]* 4.4 Write property test — Tenant deletion cascade (Property 14)
  - `// Feature: super-admin-panel, Property 14: Tenant deletion cascades to users`
  - **Validates: Requirements 7.1, 7.2**

- [x] 4.5 Create tenant management views


  - `resources/views/admin/tenants/index.blade.php` — list + delete button
  - `resources/views/admin/tenants/create.blade.php` — create form
  - _Requirements: 2.1, 3.3_

- [ ] 5. Checkpoint — Ensure all tests pass, ask the user if questions arise.

- [x] 6. Role and permission management


- [x] 6.1 Create `AdminRoleController`



  - `index(tenant)` → init tenancy for tenant, list roles + permissions from tenant DB
  - `store(tenant)` → create role in tenant DB
  - `destroy(tenant, role)` → delete role from tenant DB
  - `syncPermissions(tenant, role)` → sync permissions in tenant DB
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ]* 6.2 Write property test — Role isolation between tenants (Property 7)
  - `// Feature: super-admin-panel, Property 7: Role isolation between tenants`
  - **Validates: Requirements 4.2**

- [ ]* 6.3 Write property test — Permission sync and role deletion (Properties 8, 9)
  - `// Feature: super-admin-panel, Property 8: Permission sync updates correct tenant DB`
  - `// Feature: super-admin-panel, Property 9: Role deletion removes role and permissions`


  - **Validates: Requirements 4.3, 4.4**




- [ ] 6.4 Create role management views
  - `resources/views/admin/roles/index.blade.php` — list roles + permissions


  - `resources/views/admin/roles/create.blade.php` — create role form
  - _Requirements: 4.1_



- [ ] 7. User management
- [ ] 7.1 Create `AdminUserController`
  - `index(tenant)` → list users from central DB where `tenant_id = tenant->id`
  - `store(tenant)` → create user in central DB + assign role in tenant DB
  - `destroy(tenant, user)` → delete user from central DB
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ]* 7.2 Write property test — User list scoping (Property 10)
  - `// Feature: super-admin-panel, Property 10: User list is scoped to tenant`
  - **Validates: Requirements 5.1**


- [ ]* 7.3 Write property test — User creation and duplicate email (Properties 11, 12)
  - `// Feature: super-admin-panel, Property 11: User creation assigns correct tenant_id and role`
  - `// Feature: super-admin-panel, Property 12: Duplicate email is rejected`
  - **Validates: Requirements 5.2, 5.4**

- [x] 7.4 Create user management views

  - `resources/views/admin/users/index.blade.php`
  - `resources/views/admin/users/create.blade.php`
  - _Requirements: 5.1_

- [ ] 8. ~~Impersonation — removed per user request~~

- [x] 9. Seeder for first Super Admin







  - Add `AdminSeeder` — ينشئ admin user واحد في central DB
  - Add to `DatabaseSeeder`
  - _Requirements: 1.2_

- [ ] 10. Final Checkpoint — Ensure all tests pass, ask the user if questions arise.
