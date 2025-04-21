<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Tasks\StoreRequest;
use App\Services\TaskServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TaskController extends Controller
{
    public function __construct(private readonly TaskServiceInterface $taskService)
    {
    }

    public function store(StoreRequest $request)
    {
        $this->taskService->create($request->validated());

        return Response::json([
            'message' => 'Task created successfully'
        ]);
    }
}
