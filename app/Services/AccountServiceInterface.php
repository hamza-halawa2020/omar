<?php

namespace App\Services;

use App\DTO\AccountFilter;
use App\Models\Account;
use App\Models\Lead;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AccountServiceInterface
{
    public function getAll(AccountFilter $filter = null): LengthAwarePaginator;

    public function create(array $data): Account;

    public function getOne(int $id): Account;

    public function update(Account $account, array $data): bool;

    public function delete(Account $account): bool;

    public function pluck(string $value, string $key = null): Collection;
}
