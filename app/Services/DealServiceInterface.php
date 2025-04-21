<?php

namespace App\Services;

use App\DTO\QueryFilters\DealFilter;
use App\Models\Deal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DealServiceInterface
{
    public function getAll(DealFilter $filter = null): LengthAwarePaginator;

    public function create(array $data): Deal;

    public function getOne(int $id): Deal;

    public function update(Deal $deal, array $data): bool;

    public function delete(Deal $deal): bool;

    public function pluck(string $value, string $key = null, DealFilter $filter = null): Collection;
}
