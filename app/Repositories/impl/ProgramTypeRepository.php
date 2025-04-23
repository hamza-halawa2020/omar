<?php

namespace App\Repositories\impl;

use App\Models\ProgramType;
use App\Repositories\ProgramTypeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProgramTypeRepository implements ProgramTypeRepositoryInterface
{
    public function getAll(): LengthAwarePaginator{
        return ProgramType::query()->latest()->paginate(10);
    }

    public function create(array $data): ProgramType{
        return ProgramType::create($data);
    }

    public function getById(int $id): ProgramType{
        return ProgramType::findOrFail($id);
    }

    public function update(ProgramType $programType, array $data): bool{
        return $programType->update($data);
    }

    public function delete(ProgramType $programType): bool{
        return $programType->delete();
    }

    public function pluck(string $value, string $key = null): Collection{
        return ProgramType::query()->pluck($value, $key);
    }
}


