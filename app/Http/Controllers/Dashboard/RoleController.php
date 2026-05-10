<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Routing\Controller as BaseController;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    public function __construct(private readonly RoleService $roleService)
    {
        $this->middleware('check.permission:roles_index')->only('index');
        $this->middleware('check.permission:roles_store')->only('store', 'create');
        $this->middleware('check.permission:roles_update')->only('update', 'edit');
        $this->middleware('check.permission:roles_destroy')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.roles.index', $this->roleService->indexData());
    }

    public function create()
    {
        return view('dashboard.roles.create', $this->roleService->createData());
    }

    public function store(StoreRoleRequest $request)
    {
        $this->roleService->store($request->validated());

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        return view('dashboard.roles.edit', $this->roleService->editData((int) $id));
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $this->roleService->update((int) $id, $request->validated());

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }


    public function destroy(Role $role)
    {
        $this->roleService->destroy($role);

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
