<?php

namespace App\Services;

use App\DTO\CallFilter;
use App\Models\Call;
use App\Models\Lead;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CallServiceInterface
{
    public function getAll(CallFilter $filter = null): LengthAwarePaginator;

    public function create(array $data): Call;

    public function getOne(int $id): Call;

    public function update(Call $call, array $data): bool;

    public function delete(Call $call): bool;

    public function pluck(string $value, string $key = null, CallFilter $filter = null): Collection;
}
