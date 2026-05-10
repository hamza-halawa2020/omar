<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function indexData(): array
    {
        return ['roles' => Role::all()];
    }

    public function list(): Collection
    {
        return User::with('roles')->latest()->get();
    }

    public function store(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            if (isset($data['role'])) {
                $user->syncRoles($data['role']);
            }

            return $user->load('roles');
        });
    }

    public function show(int $id): User
    {
        $user = User::findOrFail($id);
        $user->load('roles', 'permissions');

        return $user;
    }

    public function update(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = User::findOrFail($id);

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            if (isset($data['role'])) {
                $user->syncRoles($data['role']);
            }

            return $user->load('roles');
        });
    }

    public function destroy(int $id): void
    {
        $user = User::findOrFail($id);

        if (Auth::id() === $user->id) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => __('messages.cannot_delete_yourself'),
            ], 403));
        }

        $user->delete();
    }
}
