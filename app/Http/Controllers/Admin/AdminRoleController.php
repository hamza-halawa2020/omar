<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    /**
     * List roles and permissions for a tenant — Requirements: 4.1
     */
    public function index(Tenant $tenant)
    {
        try {
            tenancy()->initialize($tenant);
            $roles       = Role::with('permissions')->get();
            $permissions = Permission::all();
        } finally {
            tenancy()->end();
        }

        return view('admin.roles.index', compact('tenant', 'roles', 'permissions'));
    }

    /**
     * Create a new role in the tenant DB — Requirements: 4.2
     */
    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            tenancy()->initialize($tenant);
            Role::create(['name' => $request->name, 'guard_name' => 'web']);
        } finally {
            tenancy()->end();
        }

        return redirect()
            ->route('admin.tenants.roles.index', $tenant)
            ->with('success', 'تم إنشاء الدور بنجاح.');
    }

    /**
     * Delete a role from the tenant DB — Requirements: 4.4
     */
    public function destroy(Tenant $tenant, int $roleId)
    {
        try {
            tenancy()->initialize($tenant);
            $role = Role::findById($roleId);
            $role->syncPermissions([]);
            $role->delete();
        } finally {
            tenancy()->end();
        }

        return redirect()
            ->route('admin.tenants.roles.index', $tenant)
            ->with('success', 'تم حذف الدور بنجاح.');
    }

    /**
     * Sync permissions for a role in the tenant DB — Requirements: 4.3
     */
    public function syncPermissions(Request $request, Tenant $tenant, int $roleId)
    {
        $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        try {
            tenancy()->initialize($tenant);
            $role = Role::findById($roleId);
            $role->syncPermissions($request->input('permissions', []));
        } finally {
            tenancy()->end();
        }

        return redirect()
            ->route('admin.tenants.roles.index', $tenant)
            ->with('success', 'تم تحديث الصلاحيات بنجاح.');
    }
}
