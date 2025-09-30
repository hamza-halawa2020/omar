<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:users_index')->only('index', 'list');
        $this->middleware('check.permission:users_store')->only('store');
        $this->middleware('check.permission:users_show')->only('show');
        $this->middleware('check.permission:users_update')->only('update');
        $this->middleware('check.permission:users_destroy')->only('destroy');
    }

    public function index()
    {
        $users = User::with('roles')->latest()->get();
        $roles = Role::all();

        return view('dashboard.users.index', compact('users', 'roles'));
    }

    public function list()
    {
        $users = User::with('roles')->latest()->get();

        return response()->json(['status' => true, 'message' => __('messages.users_fetched_successfully'), 'data' => UserResource::collection($users)]);
    }

    public function store(StoreUserRequest $request)
    {

        $data = $request->validated();

        // dd( $data);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if (isset($data['role'])) {
            $user->syncRoles($data['role']);
        }

        return response()->json(['status' => true, 'message' => __('messages.user_created_successfully'), 'data' => new UserResource($user->load('roles'))], 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $user->load('roles', 'permissions');

        return response()->json(['status' => true, 'message' => __('messages.user_fetched_successfully'), 'data' => new UserResource($user)]);
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

        return response()->json(['status' => true, 'message' => __('messages.user_updated_successfully'), 'data' => new UserResource($user->load('roles'))]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() == $user->id) {
            return response()->json([
                'status' => false,
                'message' => __('messages.cannot_delete_yourself'),
            ], 403);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.user_deleted_successfully'),
        ]);
    }
}
