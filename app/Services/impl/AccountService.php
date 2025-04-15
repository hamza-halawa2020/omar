<?php

namespace App\Services\impl;

use App\DTO\AccountFilter;
use App\Models\Account;
use App\Models\Lead;
use App\Repositories\AccountRepositoryInterface;
use App\Services\AccountServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class AccountService implements AccountServiceInterface
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function getAll(AccountFilter $filter = null): LengthAwarePaginator
    {
        return $this->accountRepository->getAll($filter);
    }

    public function create(array $data): Account
    {
        return $this->accountRepository->create($data);
    }

    public function getOne(int $id): Account
    {
        return $this->accountRepository->getById($id);
    }

    public function update(Account $account, array $data): bool
    {
        return $this->accountRepository->update($account, $data);
    }

    public function delete(Account $account): bool
    {
        return $this->accountRepository->delete($account);
    }

    public function pluck(string $value, string $key = null): Collection
    {
        return $this->accountRepository->pluck($value, $key);
    }

}
