<?php

namespace App\Repositories\impl;

use App\DTO\DealFilter;
use App\Models\Deal;
use App\Repositories\DealRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DealRepository implements DealRepositoryInterface
{

    public function getAll(DealFilter $filter = null): LengthAwarePaginator
    {
        return Deal::query()
            ->latest()
            ->paginate();
    }

    public function create(array $data): Deal
    {
        return Deal::create($data);
    }

    public function getById(int $id): Deal
    {
        return Deal::findOrFail($id);
    }

    public function update(Deal $deal, array $data): bool
    {
        return $deal->update($data);
    }

    public function delete(Deal $deal): bool
    {
        return $deal->delete();
    }

    public function pluck(string $value, string $key = null): Collection
    {
        return $key
            ? Deal::pluck($value, $key)
            : Deal::pluck($value);
    }
}
