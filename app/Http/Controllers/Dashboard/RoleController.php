<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    // public function __construct()
    // {
    //     $this->middleware('check.permission:roles_index')->only('index');
    //     $this->middleware('check.permission:roles_store')->only('store', 'create');
    //     $this->middleware('check.permission:roles_update')->only('update', 'edit');
    //     $this->middleware('check.permission:roles_destroy')->only('destroy');
    // }

    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);

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
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('dashboard.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

   public function update(Request $request, $id)
{
    $role = Role::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
    ]);

    $role->update(['name' => $request->name]);

    // Update permissions
    $role->syncPermissions($request->permissions ?? []);

    return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
}


    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
