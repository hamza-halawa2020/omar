<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserRolePermissionController extends Controller
{

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
        ]);

        if ($request->roles) {
            $roles = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
        } else {
            $roles = [];
        }

        $user->syncRoles($roles);


        return redirect()->route('user-role-permissions.index')
            ->with('success', 'User roles updated successfully.');
    }
}
