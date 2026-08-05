# Multi-Tenancy Migration Plan
## stancl/tenancy — Database-per-Tenant

---

## نظرة عامة على المشروع

Laravel 11 — Web dashboard (Blade) — MySQL — Spatie Permissions — Session Auth

**الفكرة:** كل مجموعة يوزرز (tenant) ليها داتا بيز خاصة بيها.
اليوزرز بيدخلوا من نفس الـ login page، وبعد الـ login بيتعمل switch تلقائي للداتا بيز الصح.

**طريقة تحديد الـ Tenant:** عن طريق الـ email domain تلقائياً
- اليوزر بيدخل email + password فقط (مفيش حقول إضافية)
- النظام بيستخرج الدومين من الـ email: `omar@company.com` → tenant = `company.com`
- كل tenant عنده `domain` column في جدول الـ tenants

---

## Architecture

```
Central Database (omar — DB_DATABASE في .env)
├── tenants                  ← بيانات الـ tenants (id, name, domain)
├── domains                  ← جدول الباكدج للـ domains (غير مستخدم فعلياً)
├── users                    ← كل اليوزرز مع tenant_id
├── password_reset_tokens
├── sessions
├── cache / jobs
└── migrations (central)

Tenant Database (اسمها tenant{id} مثلاً tenantabc123)
├── categories
├── payment_ways
├── payment_way_limits
├── payment_way_logs
├── transactions
├── transaction_logs
├── clients
├── client_debt_logs
├── products
├── installment_contracts
├── installments
├── installment_payments
├── associations
├── association_members
├── association_payments
├── roles / permissions / model_has_roles / model_has_permissions (spatie) ← لكل tenant لوحده
└── migrations (tenant)
```

---

## ملاحظة مهمة: User model والـ connection

الـ `User` model عنده `$connection = 'central'` ثابت لأنه مخزن في الـ central DB.
لكن الـ roles و permissions بتاعته موجودة في الـ **tenant DB**.

هذا يعني إن Spatie لو استخدم الـ `morphToMany` عادي هيعمل query على `central.permissions` وده غلط.
**الحل:** Override للـ `roles()` و `permissions()` relations في User model بحيث:
1. يتعمل `setConnection($activeConn)` مؤقتاً على `$this` قبل `morphToMany`
2. يترجع لـ `central` بعد بناء الـ relation
3. الـ related model (Role/Permission) يشتغل على `$activeConn` كمان

---

## TODO List

### ✅ المرحلة 1: التحضير والتثبيت

- [x] **Task 1** — تثبيت `stancl/tenancy` ✅
- [x] **Task 2** — تعديل `config/tenancy.php` — DatabaseBootstrapper فقط + tenant_model ✅

---

### ✅ المرحلة 2: الـ Central Database

- [x] **Task 3** — إنشاء موديل `Tenant` (`app/Models/Tenant.php`) ✅
  - بيـ extend `BaseTenant` من الباكدج
  - بيـ implement `TenantWithDatabase`
  - `getCustomColumns()` بترجع `['id', 'name', 'domain']`
  - علاقة `hasMany(User::class)`

- [x] **Task 4** — تعديل موديل `User` ✅
  - `$connection = 'central'`
  - إضافة `tenant_id` لـ `$fillable`
  - علاقة `belongsTo(Tenant::class)`
  - **Override** `roles()` relation — بتشتغل على tenant DB مؤقتاً
  - **Override** `permissions()` relation — بتشتغل على tenant DB مؤقتاً
  - **Override** `hasDirectPermission()` — بيعمل query مباشر على tenant DB

- [x] **Task 5** — migration `2026_08_05_000001_add_tenant_fields` ✅
  - `tenant_id` على جدول `users`
  - `name` و `domain` على جدول `tenants` (اللي أنشأه الباكدج)

---

### ✅ المرحلة 3: تحديد الـ Tenant عند الـ Login

- [x] **Task 6** — `LoginService` — login flow كامل ✅
  - استخراج domain من email: `Str::after($email, '@')`
  - `Tenant::where('domain', $domain)->first()` — لو مش موجود → error
  - `Auth::attempt()` — لو فشل → error
  - التحقق إن `user->tenant_id === tenant->id`
  - `session(['tenant_id' => $tenant->id])`
  - `tenancy()->initialize($tenant)`
  - `event(new CreateBackup)` بعد كل login ناجح

- [x] **Task 7** — `LoginService` — logout flow ✅
  - `tenancy()->end()` لو كان initialized
  - `Auth::logout()`
  - `session()->invalidate()`
  - `event(new CreateBackup)` عند الـ logout

---

### ✅ المرحلة 4: الـ Tenant Middleware

- [x] **Task 8** — إنشاء `InitializeTenancyBySession` Middleware ✅
  - بيجيب `tenant_id` من الـ session
  - لو مش موجود → redirect to login
  - `tenancy()->initialize($tenant)`
  - `DB::purge()` + `DB::reconnect()` لفرض reconnect على الـ tenant DB
  - `forgetCachedPermissions()` لتنظيف cache الـ Spatie

- [x] **Task 9** — تسجيل الـ middleware في `bootstrap/app.php` ✅
  - alias: `'tenancy' => InitializeTenancyBySession::class`

---

### ✅ المرحلة 5: الـ Tenant Migrations

- [x] **Task 10** — كل tenant migrations في `database/migrations/tenant/` ✅
  - categories, payment_ways, transactions, clients, products, installments, associations, إلخ
  - `create_permission_tables.php` (Spatie) موجود هنا — يعني roles/permissions في كل tenant DB

- [x] **Task 11** — `config/tenancy.php` مضبوط ✅
  ```php
  'migration_parameters' => [
      '--path' => [database_path('migrations/tenant')],
  ]
  ```

---

### ✅ المرحلة 6: Spatie Permissions مع Multi-Tenancy

- [x] **Task 12** — `config/permission.php` — `cache.store = 'array'` ✅
  - بيمنع تلوث الـ cache بين tenants مختلفين

- [x] **Task 13** — `InitializeTenancyBySession` — `forgetCachedPermissions()` بعد كل switch ✅

- [x] **Task 14** — User model — Override relations لـ Spatie ✅
  - `roles()` و `permissions()` بيشتغلوا على `config('database.default')` (الـ tenant connection)
  - `hasDirectPermission()` بيعمل query مباشر بدل `loadMissing()` اللي كان بيستخدم central

---

### ✅ المرحلة 7: إدارة الـ Tenants (Admin)

- [x] **Task 15** — `app/Services/TenantService.php` ✅
- [x] **Task 16** — `app/Http/Controllers/Dashboard/TenantController.php` ✅
- [x] **Task 17** — routes للـ tenants في `web.php` ✅
- [x] **Task 18** — `resources/views/dashboard/tenants/index.blade.php` ✅

---

### ✅ المرحلة 8: Google Drive Backup

- [x] **Task 19** — `AppServiceProvider` — تسجيل Google Drive storage driver ✅
  - الـ `try/catch` جوه الـ callback مش بره — عشان الـ exception بتتحرق وقت resolve الـ disk مش وقت register
  - لو الـ `refreshToken` مش موجود → بيعمل `info()` log وبيعمل `throw` عشان `spatie/backup` يـ handle الـ error بنفسه وييجي backup notification بالـ error

---

### ✅ المرحلة 9: Seeder وإنشاء Tenants

- [x] **Task 20** — `DatabaseSeeder` ✅
  - بينشئ Tenant 1: `example.com` — Demo Company
  - بينشئ Tenant 2: `store.com` — Store Company
  - لكل tenant: بيشغّل `PermissionSeeder` + `RolesSeeder` + `FinanceSeeder` في context الـ tenant
  - بينشئ users في central DB مع `tenant_id`
  - بيعمل `syncRoles()` لكل user في context الـ tenant DB

- [ ] **Task 21** — تشغيل الـ migrations والـ seeders (يتعمل يدوياً)
  ```bash
  # Central migrations
  php artisan migrate

  # Tenant migrations على كل tenant موجود
  php artisan tenants:migrate

  # Seeders
  php artisan db:seed
  ```

---

### 🧪 المرحلة 10: الاختبار

- [ ] **Task 22** — اختبار الـ login
  - `admin@example.com` / `12345678` → يفتح dashboard على tenant DB بتاع example.com
  - `admin@store.com` / `12345678` → يفتح dashboard على tenant DB بتاع store.com
  - Email بدومين غير موجود → error "Invalid credentials"

- [ ] **Task 23** — اختبار الـ permissions
  - Super admin يشوف كل الـ sidebar
  - Manager يشوف sections معينة بس
  - Employee يشوف أقل

- [ ] **Task 24** — اختبار عزل الداتا
  - Data اللي اتضافت في tenant 1 مش بتظهر في tenant 2

- [ ] **Task 25** — اختبار إنشاء tenant جديد من `/dashboard/tenants`
  - بعد الإنشاء تشغيل `php artisan tenants:migrate --tenants={id}`

---

## الـ Login Flow (كامل)

```
User enters (email + password)
→ Extract domain: omar@company.com → company.com
→ Tenant::where('domain', 'company.com')->first()
→ لو مش موجود → error "Invalid credentials"
→ Auth::attempt(['email', 'password'])
→ لو فشل → error "Invalid credentials"
→ تحقق: user->tenant_id === tenant->id
→ لو مش متطابق → Auth::logout() + error
→ session()->regenerate()
→ session(['tenant_id' => $tenant->id])
→ tenancy()->initialize($tenant)   ← بيعمل switch للـ tenant DB
→ event(new CreateBackup)
→ redirect('/dashboard')
```

```
Subsequent requests (dashboard pages)
→ InitializeTenancyBySession middleware
→ يجيب tenant_id من session
→ tenancy()->initialize($tenant)
→ DB::purge() + DB::reconnect()   ← يضمن إن الـ connection الجديد شغال
→ forgetCachedPermissions()       ← ينظف Spatie cache
→ Request proceeds على tenant DB
```

---

## ملاحظات تقنية مهمة

1. **الـ User model connection** — `$connection = 'central'` ثابت دايماً. الـ Spatie relations (roles/permissions) بيتعملوا override عشان يشتغلوا على `config('database.default')` اللي بيتغير وقت tenancy initialization.

2. **morphToMany والـ connection** — الـ `morphToMany` بيبني الـ query باستخدام `$this->getConnection()` وقت الاستدعاء. عشان كدا لازم `setConnection($activeConn)` على `$this` قبل الاستدعاء وترجعه بعدين.

3. **Spatie cache** — لازم `array` store مش `redis` أو `file` عشان متختلطش الـ cache بين tenants مختلفين في نفس الـ request cycle.

4. **الـ sessions** — محتاجة تفضل في الـ central DB مش في tenant DB.

5. **Google Drive Backup** — الـ backup بيتعمل تلقائياً عند كل login وlogout. لو `GOOGLE_DRIVE_REFRESH_TOKEN` مش موجود في `.env` الـ local backup بيشتغل بس الـ Google Drive بيفشل بـ error مسجل في الـ log.

6. **إنشاء tenant جديد** — بعد ما تنشئه من الـ dashboard، لازم تشغّل الـ tenant migrations يدوياً:
   ```bash
   php artisan tenants:migrate --tenants={tenant_id}
   ```

---

## الملفات المعدّلة

| الملف | التعديل |
|---|---|
| `app/Models/User.php` | `$connection = 'central'` + override roles/permissions + hasDirectPermission |
| `app/Models/Tenant.php` | جديد — extend BaseTenant |
| `app/Services/LoginService.php` | tenant identification + init + backup event |
| `app/Services/TenantService.php` | جديد — CRUD للـ tenants |
| `app/Http/Middleware/InitializeTenancyBySession.php` | جديد — tenant init per request |
| `app/Http/Controllers/Dashboard/TenantController.php` | جديد |
| `app/Providers/AppServiceProvider.php` | Google Drive driver registration (try/catch داخل الـ callback) |
| `bootstrap/app.php` | alias: `tenancy` middleware |
| `config/tenancy.php` | DatabaseBootstrapper + tenant migrations path |
| `config/permission.php` | `cache.store = 'array'` |
| `database/migrations/` | central migrations فقط |
| `database/migrations/tenant/` | كل tenant migrations + spatie permission tables |
| `database/seeders/DatabaseSeeder.php` | ينشئ 2 tenants + users + roles |
