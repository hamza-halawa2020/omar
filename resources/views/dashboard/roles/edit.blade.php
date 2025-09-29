@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div>{{ __('messages.edit_role') }}</div>
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>{{ __('messages.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="d-block mb-2">{{ __('messages.permissions') }}</label>
                <div class="row">
                    @foreach ($permissions as $permission)
                        <div class="d-flex col-md-3 col-sm-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                    value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                    {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                <label class="form-check-label ms-1" for="perm_{{ $permission->id }}">
                                    {{ __('messages.' . $permission->name) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button class="btn btn-outline-success btn-sm radius-8">{{ __('messages.update') }}</button>
        </form>
    </div>
@endsection
