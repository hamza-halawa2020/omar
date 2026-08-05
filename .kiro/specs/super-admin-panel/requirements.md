# Requirements Document

## Introduction

إضافة نظام **Super Admin** منفصل تماماً عن الـ tenants العاديين.
الـ Super Admin هو مسئول النظام بالكامل — بيدخل من route مختلف، عنده panel خاص بيه، ويقدر يدير كل الـ tenants وصلاحياتهم، وكمان يتنقل لـ dashboard أي tenant ويشوف بياناته.

الـ Super Admin مخزون في الـ **central DB** بدون `tenant_id`، ومفيش علاقة له بأي tenant.

---

## Glossary

- **Super Admin**: مسئول النظام — يدخل من `/admin` ويدير كل الـ tenants.
- **Tenant**: شركة أو مؤسسة عندها database منفصلة في النظام.
- **Tenant User**: يوزر عادي مرتبط بـ tenant معين.
- **Super Admin Panel**: لوحة التحكم الخاصة بالـ Super Admin على `/admin/dashboard`.
- **Tenant Impersonation**: قدرة الـ Super Admin على الدخول لـ dashboard أي tenant.
- **Central DB**: قاعدة البيانات المركزية اللي بتحتوي على users وtenants.
- **Tenant DB**: قاعدة البيانات الخاصة بكل tenant وبتحتوي على roles/permissions وبياناته.
- **Guard**: آلية المصادقة في Laravel — بنستخدم guard منفصل للـ Super Admin.

---

## Requirements

### Requirement 1

**User Story:** As a Super Admin, I want to log in from a separate route (`/admin/login`), so that my authentication is completely isolated from tenant users.

#### Acceptance Criteria

1. WHEN a Super Admin visits `/admin/login`, THE System SHALL display a dedicated login form.
2. WHEN a Super Admin submits valid credentials, THE System SHALL authenticate using a separate `admin` guard and redirect to `/admin/dashboard`.
3. WHEN a Super Admin submits invalid credentials, THE System SHALL return an error without affecting tenant sessions.
4. IF a non-Super Admin attempts to access `/admin/*` routes, THEN THE System SHALL redirect the request to `/admin/login`.
5. WHEN a Super Admin logs out, THE System SHALL invalidate the admin session only, without affecting any active tenant sessions.

---

### Requirement 2

**User Story:** As a Super Admin, I want a dedicated panel at `/admin/dashboard`, so that I can manage all tenants from one place.

#### Acceptance Criteria

1. WHEN a Super Admin accesses `/admin/dashboard`, THE System SHALL display a list of all tenants with their name, domain, and creation date.
2. WHILE a Super Admin is authenticated, THE System SHALL protect all `/admin/*` routes using the `admin` guard middleware.
3. WHEN a Super Admin accesses any `/admin/*` route without authentication, THE System SHALL redirect to `/admin/login`.

---

### Requirement 3

**User Story:** As a Super Admin, I want to create new tenants, so that I can onboard new companies to the system.

#### Acceptance Criteria

1. WHEN a Super Admin submits a valid tenant creation form (name + domain), THE System SHALL create a new tenant record in the central DB and run `tenants:migrate` automatically on the new tenant database.
2. IF a domain already exists in the tenants table, THEN THE System SHALL reject the creation request and return a validation error.
3. WHEN a tenant is created successfully, THE System SHALL display the new tenant in the tenants list immediately.

---

### Requirement 4

**User Story:** As a Super Admin, I want to manage roles and permissions for any tenant, so that I can control access levels within each tenant's system.

#### Acceptance Criteria

1. WHEN a Super Admin selects a tenant, THE System SHALL display the roles and permissions defined in that tenant's database.
2. WHEN a Super Admin creates a new role for a tenant, THE System SHALL insert the role into that tenant's database only, not the central DB or other tenant DBs.
3. WHEN a Super Admin assigns permissions to a role, THE System SHALL update the `role_has_permissions` table in that tenant's database.
4. WHEN a Super Admin deletes a role, THE System SHALL remove the role and its associated permissions from that tenant's database.

---

### Requirement 5

**User Story:** As a Super Admin, I want to manage users for any tenant, so that I can add, edit, or remove users from any company.

#### Acceptance Criteria

1. WHEN a Super Admin views a tenant's users, THE System SHALL list all users in the central DB whose `tenant_id` matches that tenant.
2. WHEN a Super Admin creates a new user for a tenant, THE System SHALL insert the user in the central DB with the correct `tenant_id` and assign the selected role in that tenant's DB.
3. WHEN a Super Admin deletes a user, THE System SHALL remove the user from the central DB.
4. IF a Super Admin attempts to create a user with an email that already exists in the central DB, THEN THE System SHALL reject the request with a validation error.

---

### Requirement 6

**User Story:** As a Super Admin, I want to impersonate any tenant, so that I can view and verify their dashboard data.

#### Acceptance Criteria

1. WHEN a Super Admin clicks "Enter as Tenant" for a specific tenant, THE System SHALL initialize tenancy for that tenant and redirect to the tenant's dashboard.
2. WHILE a Super Admin is impersonating a tenant, THE System SHALL display a visible banner indicating that impersonation mode is active.
3. WHEN a Super Admin clicks "Exit Tenant", THE System SHALL end the tenant session and redirect back to `/admin/dashboard`.
4. IF tenancy initialization fails for a tenant, THEN THE System SHALL display an error message and remain on the admin panel.

---

### Requirement 7

**User Story:** As a Super Admin, I want to delete tenants, so that I can remove companies that are no longer using the system.

#### Acceptance Criteria

1. WHEN a Super Admin confirms deletion of a tenant, THE System SHALL delete the tenant record from the central DB and drop the tenant's database.
2. WHEN a tenant is deleted, THE System SHALL also delete all users in the central DB whose `tenant_id` matches the deleted tenant.
3. IF a tenant has active sessions at the time of deletion, THEN THE System SHALL invalidate those sessions.
