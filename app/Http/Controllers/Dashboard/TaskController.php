<?php

namespace App\Http\Controllers\Dashboard;

use App\DTO\TaskFilter;
use App\Enums\Tasks\RelatedToType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Tasks\StoreRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskServiceInterface;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private readonly TaskServiceInterface $taskService)
    {
    }

    public function index()
    {
        $tasks = $this->taskService->getAll(new TaskFilter(withRelatedTo: true, withAssignedUser: true));

        return view('dashboard.crud.tasks.index', [
            'tasks' => $tasks,
            'title' => 'Tasks',
            'subTitle' => 'All tasks'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Task $task)
    {
        $usersSelect = User::pluck('full_name', 'id');

        return view('dashboard.crud.tasks.create', [
            'task' => $task,
            'usersSelect' => $usersSelect,
            'title' => 'Tasks',
            'subTitle' => 'Create task'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->taskService->create($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $usersSelect = User::pluck('full_name', 'id');

        return view('dashboard.crud.tasks.edit', [
            'task' => $task,
            'usersSelect' => $usersSelect,
            'title' => 'Tasks',
            'subTitle' => 'Edit task'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, Task $task)
    {
        $this->taskService->update($task, $request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->taskService->delete($task);

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
