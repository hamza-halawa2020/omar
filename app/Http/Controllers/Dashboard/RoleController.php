<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controller as BaseController;

class RoleController extends BaseController
{

    public function __construct()
    {
        $this->middleware('check.permission:general_roles_index')->only('index');
        $this->middleware('check.permission:general_roles_show')->only('show');
        $this->middleware('check.permission:general_roles_create')->only(['create', 'store']);
        $this->middleware('check.permission:general_roles_update')->only(['edit', 'update']);
        $this->middleware('check.permission:general_roles_destroy')->only('destroy');
    }



    public function index(Request $request)
    {
        $query = Role::query();
        $query->with('permissions');

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        $perPage = $request->query('per_page', 10);
        $roles = $query->paginate($perPage);
        return view('dashboard.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('dashboard.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
            'is_editable' => 'required|boolean',
        ]);

        $role = Role::create(['name' => $request->name,'is_editable' => $request->is_editable]);

        $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('dashboard.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
        ]);

        $role->update(['name' => $request->name]);

        $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id); // Use findOrFail to throw a 404 if not found
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role because it is assigned to users.');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
