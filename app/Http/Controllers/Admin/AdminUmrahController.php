<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Support\UmrahTenantContext;
use Database\Seeders\UmrahPermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminUmrahController extends Controller
{
    public function edit(Tenant $tenant)
    {
        abort_unless($tenant->isUmrah(), 404);
        $users = User::on('central')->where('tenant_id', $tenant->id)->get();
        [$roles, $userRoles] = UmrahTenantContext::run($tenant, function () use ($users) {
            $roles = Role::where('guard_name', 'web')->whereIn('name', UmrahPermissionSeeder::ROLE_NAMES)->pluck('name');
            $userRoles = $users->mapWithKeys(fn ($user) => [$user->id => $user->roles()->whereIn('name', $roles)->value('name')])->all();

            return [$roles, $userRoles];
        });

        return view('admin.tenants.umrah', compact('tenant', 'users', 'roles', 'userRoles'));
    }

    public function user(Request $request, Tenant $tenant, User $user)
    {
        abort_unless($tenant->isUmrah() && (string) $user->tenant_id === (string) $tenant->id, 404);
        $data = $request->validate(['umrah_role' => ['nullable', 'string', 'max:255'],
            'umrah_only' => ['prohibited'], 'default_module' => ['prohibited'], 'umrah_enabled' => ['prohibited']]);
        UmrahTenantContext::run($tenant, function () use ($user, $data) {
            DB::transaction(function () use ($user, $data) {
                $roles = Role::whereIn('name', UmrahPermissionSeeder::ROLE_NAMES)->where('guard_name', 'web')->get();
                if (! empty($data['umrah_role'])) {
                    abort_unless($roles->contains('name', $data['umrah_role']), 422);
                }
                foreach ($roles as $role) {
                    $user->removeRole($role);
                }
                if (! empty($data['umrah_role'])) {
                    $user->assignRole($data['umrah_role']);
                }
            });
        });

        return back()->with('success', __('umrah.role_saved'));
    }
}
