<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function __construct(private readonly UserService $userService)
    {
        $this->middleware('check.permission:users_index')->only('index', 'list');
        $this->middleware('check.permission:users_store')->only('store');
        $this->middleware('check.permission:users_show')->only('show');
        $this->middleware('check.permission:users_update')->only('update');
        $this->middleware('check.permission:users_destroy')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.users.index', $this->userService->indexData());
    }

    public function list()
    {
        $users = $this->userService->list();

        return response()->json(['status' => true, 'message' => __('messages.users_fetched_successfully'), 'data' => UserResource::collection($users)]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->store($request->validated());

        return response()->json(['status' => true, 'message' => __('messages.user_created_successfully'), 'data' => new UserResource($user)], 201);
    }

    public function show($id)
    {
        $user = $this->userService->show((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.user_fetched_successfully'), 'data' => new UserResource($user)]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->update((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.user_updated_successfully'), 'data' => new UserResource($user)]);
    }

    public function destroy($id)
    {
        $this->userService->destroy((int) $id);

        return response()->json(['status' => true,'message' => __('messages.user_deleted_successfully')]);
    }
}
