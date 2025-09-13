<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->get();

        return response()->json(['status'  => true, 'message' => 'Users fetched successfully', 'data' => UserResource::collection($users)]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return response()->json(['status'  => true, 'message' => 'User created successfully', 'data' => new UserResource($user->load('roles'))], 201);
    }

    public function show(User $user)
    {
        $user->load('roles', 'permissions');

        return response()->json(['status'  => true, 'message' => 'User fetched successfully', 'data' => new UserResource($user)]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return response()->json(['status'  => true, 'message' => 'User updated successfully', 'data' => new UserResource($user->load('roles'))]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['status'  => true, 'message' => 'User deleted successfully']);
    }
}
