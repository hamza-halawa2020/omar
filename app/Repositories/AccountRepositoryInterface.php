<?php

namespace App\Repositories;

use App\DTO\QueryFilters\AccountFilter;
use App\Models\Account;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AccountRepositoryInterface
{
    public function getAll(AccountFilter $filter = null): LengthAwarePaginator;

    public function create(array $data): Account;

    public function getById(int $id): Account;

    public function update(Account $account, array $data): bool;

    public function delete(Account $account): bool;

    public function pluck(string $value, string $key = null, AccountFilter $filter = null): Collection;

}
