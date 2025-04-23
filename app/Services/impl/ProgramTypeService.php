<?php

namespace App\Services\impl;

use App\Models\ProgramType;
use App\Repositories\ProgramTypeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Collection;

class ProgramTypeRepository implements ProgramTypeRepositoryInterface
{

    public function __construct(private ProgramTypeRepositoryInterface $programTypeRepositoryInterface){}


    public function getAll(): LengthAwarePaginator{
        return  $this->programTypeRepositoryInterface->getAll();
    }

    public function create(array $data): ProgramType{
        return $this->programTypeRepositoryInterface->create($data);
    }

    public function getById(int $id): ProgramType{
        return $this->programTypeRepositoryInterface->getById($id);
    }

    public function update(ProgramType $programType, array $data): bool{
        return $this->programTypeRepositoryInterface->update($programType, $data);
    }

    public function delete(ProgramType $programType): bool{
        return $this->programTypeRepositoryInterface->delete($programType);
    }

    public function pluck(string $value, string $key = null): Collection{
        return $this->programTypeRepositoryInterface->pluck($value, $key);
    }
}


