<?php

namespace App\Services;

use App\DTO\QueryFilters\TaskFilter;
use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TaskServiceInterface
{
    public function getAll(TaskFilter $filter = null): LengthAwarePaginator;

    public function create(array $data): Task;

    public function getOne(int $id): Task;

    public function update(Task $task, array $data): bool;

    public function delete(Task $task): bool;

    public function pluck(string $value, string $key = null, TaskFilter $filter = null): Collection;
}
