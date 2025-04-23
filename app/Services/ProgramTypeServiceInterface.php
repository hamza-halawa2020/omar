<?php

namespace App\Services;

use App\Models\ProgramType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProgramTypeServiceInterface
{
    public function getAll(): LengthAwarePaginator;

    public function create(array $data): ProgramType;

    public function getById(int $id): ProgramType;

    public function update(ProgramType $programType, array $data): bool;

    public function delete(ProgramType $programType): bool;

    // public function pluck(string $value, string $key = null): Collection;
}
