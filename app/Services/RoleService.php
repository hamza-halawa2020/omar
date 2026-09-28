<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function indexData(Request $request): array
    {
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = strtolower($request->input('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) $request->input('per_page', 10), 1), 100);
        $query = Role::query()->withCount('permissions');

        if ($sortBy === 'permissions') {
            $query->orderBy('permissions_count', $sortDirection);
        } elseif (in_array($sortBy, ['id', 'name', 'created_at', 'updated_at'], true)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        return ['roles' => $query->orderBy('id', 'desc')->paginate($perPage)->appends($request->query())];
    }

    public function createData(): array
    {
        return ['permissions' => $this->permissionsForForm()];
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
            'permissions' => $this->permissionsForForm(),
            'rolePermissions' => $role->permissions()->pluck('name')->toArray(),
        ];
    }

    private function permissionsForForm(): Collection
    {
        return DB::table(config('permission.table_names.permissions'))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
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
