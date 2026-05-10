<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function indexData(): array
    {
        return ['roles' => Role::with('permissions')->paginate(10)];
    }

    public function createData(): array
    {
        return ['permissions' => Permission::all()];
    }

    public function store(array $data): void
    {
        DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
            if (! empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }
        });
    }

    public function editData(int $id): array
    {
        $role = Role::findOrFail($id);

        return [
            'role' => $role,
            'permissions' => Permission::all(),
            'rolePermissions' => $role->permissions->pluck('name')->toArray(),
        ];
    }

    public function update(int $id, array $data): void
    {
        DB::transaction(function () use ($id, $data) {
            $role = Role::findOrFail($id);
            $role->update(['name' => $data['name']]);
            $role->syncPermissions($data['permissions'] ?? []);
        });
    }

    public function destroy(Role $role): void
    {
        $role->delete();
    }
}
