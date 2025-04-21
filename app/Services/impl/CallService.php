<?php

namespace App\Services\impl;

use App\DTO\QueryFilters\CallFilter;
use App\Models\Call;
use App\Repositories\CallRepositoryInterface;
use App\Services\CallServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CallService implements CallServiceInterface
{

    public function __construct(private readonly CallRepositoryInterface $callRepository)
    {
    }

    public function getAll(CallFilter $filter = null): LengthAwarePaginator
    {
        return $this->callRepository->getAll($filter);
    }

    public function create(array $data): Call
    {
        return $this->callRepository->create($data);
    }

    public function getOne(int $id): Call
    {
        return $this->callRepository->getById($id);
    }

    public function update(Call $call, array $data): bool
    {
        return $this->callRepository->update($call, $data);
    }

    public function delete(Call $call): bool
    {
        return $this->callRepository->delete($call);
    }

    public function pluck(string $value, string $key = null, CallFilter $filter = null): Collection
    {
        return $this->callRepository->pluck($value, $key, $filter);
    }
}
