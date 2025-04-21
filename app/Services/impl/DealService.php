<?php

namespace App\Services\impl;

use App\DTO\QueryFilters\DealFilter;
use App\Models\Deal;
use App\Repositories\DealRepositoryInterface;
use App\Services\DealServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class DealService implements DealServiceInterface
{

    public function __construct(private readonly DealRepositoryInterface $dealRepository)
    {
    }

    public function getAll(DealFilter $filter = null): LengthAwarePaginator
    {
        return $this->dealRepository->getAll($filter);
    }

    public function create(array $data): Deal
    {
        return $this->dealRepository->create($data);
    }

    public function getOne(int $id): Deal
    {
        return $this->dealRepository->getById($id);
    }

    public function update(Deal $deal, array $data): bool
    {
        return $this->dealRepository->update($deal, $data);
    }

    public function delete(Deal $deal): bool
    {
        return $this->dealRepository->delete($deal);
    }

    public function pluck(string $value, string $key = null, DealFilter $filter = null): Collection
    {
        return $this->dealRepository->pluck($value, $key, $filter);
    }
}
