<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function indexData(): array
    {
        return ['roles' => Role::withCount('permissions')->paginate(10)];
    }

    public function createData(): array
    {
        return [];
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
            'rolePermissions' => $role->permissions()->pluck('name')->toArray(),
        ];
    }

    public function permissionOptions(Request $request): array
    {
        $search = trim((string) $request->input('search', ''));
        $limit = min(max((int) $request->input('limit', 100), 1), 100);

        return Permission::query()
            ->select('name')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->limit($limit)
            ->pluck('name')
            ->map(fn (string $name) => [
                'id' => $name,
                'text' => __('messages.'.$name),
            ])
            ->values()
            ->all();
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
