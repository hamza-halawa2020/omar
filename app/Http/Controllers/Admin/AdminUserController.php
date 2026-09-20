<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Tenant $tenant)
    {
        $users = User::on('central')
            ->where('tenant_id', $tenant->id)
            ->get();

        return view('admin.users.index', compact('tenant', 'users'));
    }

    public function create(Tenant $tenant)
    {
        try {
            tenancy()->initialize($tenant);
            $roles = $this->tenantRoles();
        } finally {
            tenancy()->end();
        }

        return view('admin.users.create', compact('tenant', 'roles'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:central.users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::on('central')->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->filled('role')) {
            try {
                tenancy()->initialize($tenant);
                $this->syncTenantUserRole($user, $request->role);
            } finally {
                tenancy()->end();
            }
        }

        return redirect()
            ->route('admin.tenants.users.index', $tenant)
            ->with('success', __('messages.admin.user_created'));
    }

    public function edit(Tenant $tenant, User $user)
    {
        abort_unless($user->tenant_id === $tenant->id, 404);

        try {
            tenancy()->initialize($tenant);
            $roles = $this->tenantRoles();
            $currentRole = $this->currentTenantRoleName($user);
        } finally {
            tenancy()->end();
        }

        return view('admin.users.edit', compact('tenant', 'user', 'roles', 'currentRole'));
    }

    public function update(Request $request, Tenant $tenant, User $user)
    {
        abort_unless($user->tenant_id === $tenant->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('central.users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        try {
            tenancy()->initialize($tenant);
            $this->syncTenantUserRole($user, $request->input('role'));
        } finally {
            tenancy()->end();
        }

        if (! $user->is_active) {
            DB::connection('central')->table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()
            ->route('admin.tenants.users.index', $tenant)
            ->with('success', __('messages.user_updated_successfully'));
    }

    public function destroy(Tenant $tenant, User $user)
    {
        abort_unless($user->tenant_id === $tenant->id, 404);

        try {
            tenancy()->initialize($tenant);
            $this->deleteTenantUserRoles($user);
        } finally {
            tenancy()->end();
        }

        DB::connection('central')->table('sessions')->where('user_id', $user->id)->delete();
        $user->deleteQuietly();

        return back()->with('success', __('messages.admin.user_deleted'));
    }

    public function updateStatus(Request $request, Tenant $tenant, User $user)
    {
        abort_unless($user->tenant_id === $tenant->id, 404);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $user->forceFill(['is_active' => (bool) $validated['is_active']])->save();

        if (! $user->is_active) {
            DB::connection('central')->table('sessions')->where('user_id', $user->id)->delete();
        }

        return back()->with('success', __('messages.admin.user_status_updated'));
    }

    private function tenantPermissionTablesExist(): bool
    {
        $tableNames = config('permission.table_names');
        $schema = DB::connection('tenant')->getSchemaBuilder();

        return $schema->hasTable($tableNames['roles'])
            && $schema->hasTable($tableNames['model_has_roles']);
    }

    private function tenantRoles()
    {
        if (! $this->tenantPermissionTablesExist()) {
            return collect();
        }

        return DB::connection('tenant')
            ->table(config('permission.table_names.roles'))
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    private function currentTenantRoleName(User $user): ?string
    {
        if (! $this->tenantPermissionTablesExist()) {
            return null;
        }

        $tableNames = config('permission.table_names');
        $modelKey = config('permission.column_names.model_morph_key');
        $roleKey = config('permission.column_names.role_pivot_key') ?: 'role_id';

        return DB::connection('tenant')
            ->table($tableNames['model_has_roles'])
            ->join($tableNames['roles'], "{$tableNames['roles']}.id", '=', "{$tableNames['model_has_roles']}.{$roleKey}")
            ->where("{$tableNames['model_has_roles']}.{$modelKey}", $user->id)
            ->where("{$tableNames['model_has_roles']}.model_type", User::class)
            ->value("{$tableNames['roles']}.name");
    }

    private function syncTenantUserRole(User $user, ?string $roleName): void
    {
        if (! $this->tenantPermissionTablesExist()) {
            return;
        }

        $this->deleteTenantUserRoles($user);

        if (! $roleName) {
            return;
        }

        $tableNames = config('permission.table_names');
        $modelKey = config('permission.column_names.model_morph_key');
        $roleKey = config('permission.column_names.role_pivot_key') ?: 'role_id';
        $roleId = DB::connection('tenant')
            ->table($tableNames['roles'])
            ->where('name', $roleName)
            ->where('guard_name', 'web')
            ->value('id');

        if (! $roleId) {
            return;
        }

        DB::connection('tenant')->table($tableNames['model_has_roles'])->insertOrIgnore([
            $roleKey => $roleId,
            'model_type' => User::class,
            $modelKey => $user->id,
        ]);
    }

    private function deleteTenantUserRoles(User $user): void
    {
        if (! $this->tenantPermissionTablesExist()) {
            return;
        }

        $tableNames = config('permission.table_names');
        $modelKey = config('permission.column_names.model_morph_key');

        DB::connection('tenant')
            ->table($tableNames['model_has_roles'])
            ->where($modelKey, $user->id)
            ->where('model_type', User::class)
            ->delete();
    }
}
