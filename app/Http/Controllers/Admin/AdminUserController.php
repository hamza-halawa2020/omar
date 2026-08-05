<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    /**
     * List users for a specific tenant — Requirements: 5.1
     */
    public function index(Tenant $tenant)
    {
        $users = User::on('central')
            ->where('tenant_id', $tenant->id)
            ->get();

        return view('admin.users.index', compact('tenant', 'users'));
    }

    /**
     * Show create user form
     */
    public function create(Tenant $tenant)
    {
        tenancy()->initialize($tenant);
        $roles = Role::all();
        tenancy()->end();

        return view('admin.users.create', compact('tenant', 'roles'));
    }

    /**
     * Store a new user and assign role in tenant DB — Requirements: 5.2, 5.4
     */
    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:central.users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['nullable', 'string'],
        ]);

        // Create user in central DB with correct tenant_id
        $user = User::on('central')->create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'tenant_id' => $tenant->id,
        ]);

        // Assign role in tenant DB
        if ($request->filled('role')) {
            tenancy()->initialize($tenant);
            $user->assignRole($request->role);
            tenancy()->end();
        }

        return redirect()
            ->route('admin.tenants.users.index', $tenant)
            ->with('success', 'تم إنشاء المستخدم بنجاح.');
    }

    /**
     * Delete a user from central DB — Requirements: 5.3
     */
    public function destroy(Tenant $tenant, User $user)
    {
        $user->delete();

        return back()->with('success', 'تم حذف المستخدم بنجاح.');
    }
}
