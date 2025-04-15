<?php

namespace App\Services\impl;

use App\DTO\TaskFilter;
use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use App\Services\TaskServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class TaskService implements TaskServiceInterface
{
    public function __construct(private TaskRepositoryInterface $taskRepository)
    {

    }

    public function getAll(TaskFilter $filter = null): LengthAwarePaginator
    {
        return $this->taskRepository->getAll($filter);
    }

    public function create(array $data): Task
    {
        return $this->taskRepository->create($data);
    }

    public function getOne(int $id): Task
    {
        return $this->taskRepository->getById($id);
    }

    public function update(Task $task, array $data): bool
    {
        return $this->taskRepository->update($task, $data);
    }

    public function delete(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    public function pluck(string $value, string $key = null, TaskFilter $filter = null): Collection
    {
        return $this->taskRepository->pluck($value, $key, $filter);
    }
}
