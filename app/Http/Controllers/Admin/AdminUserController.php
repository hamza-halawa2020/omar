<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
        try {
            tenancy()->initialize($tenant);
            $roles = Role::all();
        } finally {
            tenancy()->end();
        }

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
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Create user in central DB with correct tenant_id
        $user = User::on('central')->create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'tenant_id' => $tenant->id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Assign role in tenant DB
        if ($request->filled('role')) {
            try {
                tenancy()->initialize($tenant);
                $user->assignRole($request->role);
            } finally {
                tenancy()->end();
            }
        }

        return redirect()
            ->route('admin.tenants.users.index', $tenant)
            ->with('success', __('messages.admin.user_created'));
    }

    /**
     * Delete a user from central DB — Requirements: 5.3
     */
    public function destroy(Tenant $tenant, User $user)
    {
        abort_unless($user->tenant_id === $tenant->id, 404);

        try {
            tenancy()->initialize($tenant);
            $user->syncRoles([]);
        } finally {
            tenancy()->end();
        }

        $user->delete();

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
}
