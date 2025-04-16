<?php

namespace App\Repositories\impl;

use App\DTO\TaskFilter;
use App\Models\Task;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{

    public function getAll(TaskFilter $filter = null): LengthAwarePaginator
    {
        return Task::query()
            ->when($filter?->withRelatedTo, function ($query) use ($filter) {
                return $query->withRelatedTo();
            })
            ->when($filter?->withAssignedUser, function ($query) use ($filter) {
                return $query->withAssignedUser();
            })
            ->latest()
            ->paginate($filter?->perPage ?? 10, ['*'], 'tasks_page')
            ->appends(preserveOtherPagination('tasks_page'));
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function getById(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function pluck(string $value, string $key = null, TaskFilter $filter = null): Collection
    {
        $query = Task::query()
            ->when($filter?->withRelatedTo, function ($query) use ($filter) {
                return $query->withRelatedTo();
            })
            ->when($filter?->withAssignedUser, function ($query) use ($filter) {
                return $query->withAssignedUser();
            });

        return $key
            ? $query->pluck($value, $key)
            : $query->pluck($value);
    }
}
