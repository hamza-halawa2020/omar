<?php

namespace App\Repositories\impl;

use App\DTO\AccountFilter;
use App\Models\Account;
use App\Models\Lead;
use App\Repositories\AccountRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AccountRepository implements AccountRepositoryInterface
{

    public function getAll(AccountFilter $filter = null): LengthAwarePaginator
    {
        return Account::query()
            ->when($filter?->withAssignedUser, function ($query) use ($filter) {
                return $query->withAssignedUser();
            })
            ->latest()
            ->paginate($filter->perPage, ['*'], 'accounts_page');
    }

    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function getById(int $id): Account
    {
        return Account::findOrFail($id);
    }

    public function update(Account $account, array $data): bool
    {
        return $account->update($data);
    }

    public function delete(Account $account): bool
    {
        return $account->delete();
    }

    public function pluck(string $value, string $key = null, AccountFilter $filter = null): Collection
{
    $query = Account::query()
    ->when($filter?->withAssignedUser, function ($query) use ($filter) {
        return $query->withAssignedUser();
    });
    return $key
        ? $query->pluck($value, $key)
        : $query->pluck($value);
}

}
