<?php

namespace App\Repositories\impl;

use App\DTO\QueryFilters\CallFilter;
use App\Models\Call;
use App\Repositories\CallRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CallRepository implements CallRepositoryInterface
{

    public function getAll(CallFilter $filter = null): LengthAwarePaginator
    {
        return Call::query()
            ->latest()
            ->paginate($filter?->perPage ?? 10, ['*'], 'calls_page');
    }

    public function create(array $data): Call
    {
        return Call::create($data);
    }

    public function getById(int $id): Call
    {
        return Call::findOrFail($id);
    }

    public function update(Call $call, array $data): bool
    {
        return $call->update($data);
    }

    public function delete(Call $call): bool
    {
        return $call->delete();
    }

    public function pluck(string $value, string $key = null, CallFilter $filter = null): Collection
    {
        $query = Call::query();

        return $key
            ? $query->pluck($value, $key)
            : $query->pluck($value);
    }
}
