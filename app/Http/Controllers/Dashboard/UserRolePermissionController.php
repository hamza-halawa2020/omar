<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserRolePermissionController extends BaseController
{



    // public function __construct()
    // {
    //     $this->middleware('check.permission:general_user_role_permissions_index')->only('index');
    //     $this->middleware('check.permission:general_user_role_permissions_update')->only('edit');
    //     $this->middleware('check.permission:general_user_role_permissions_update')->only('update');
    // }

    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('dashboard.user_role_permissions.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('dashboard.user_role_permissions.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'roles' => 'array|nullable',
            'project_access' => 'array|nullable',
        ]);

        if ($request->roles) {
            $roles = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
        } else {
            $roles = [];
        }

        $user->syncRoles($roles);
        $user->project_access =  json_encode($request->project_access);
        $user->save();


        return redirect()->route('user-role-permissions.index')
            ->with('success', 'User roles updated successfully.');
    }
}
