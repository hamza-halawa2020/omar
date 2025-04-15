<?php

namespace App\Repositories;

use App\DTO\DealFilter;
use App\Models\Deal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DealRepositoryInterface
{
    public function getAll(DealFilter $filter = null): LengthAwarePaginator;

    public function create(array $data): Deal;

    public function getById(int $id): Deal;

    public function update(Deal $deal, array $data): bool;

    public function delete(Deal $deal): bool;

    public function pluck(string $value, string $key = null): Collection;
}
