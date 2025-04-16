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
            ->when($filter?->withAccount, function ($query) use ($filter) {
                return $query->withAccount();
            })
            ->when($filter?->withContact, function ($query) use ($filter) {
                return $query->withContact();
            })
            ->when($filter?->withTasks, function ($query) use ($filter) {
                return $query->withTasks();
            })
            ->latest()
            ->paginate($filter->perPage, ['*'], 'deals_page')
            ->appends(preserveOtherPagination('deals_page'));
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

    public function pluck(string $value, string $key = null, DealFilter $filter = null): Collection
    {
        $query = Deal::query()
            ->when($filter?->withAccount, function ($query) use ($filter) {
                return $query->withAccount();
            })
            ->when($filter?->withContact, function ($query) use ($filter) {
                return $query->withContact();
            })
            ->when($filter?->withTasks, function ($query) use ($filter) {
                return $query->withTasks();
            })
            ->when($filter?->accountId, function ($query) use ($filter) {
                return $query->where('account_id', $filter->accountId);
            })
            ->when($filter?->contactId, function ($query) use ($filter) {
                return $query->where('contact_id', $filter->contactId);
            });

        return $key
            ? $query->pluck($value, $key)
            : $query->pluck($value);
    }
}
