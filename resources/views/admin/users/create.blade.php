@extends('admin.layouts.app')

@section('title', __('messages.admin.add_user_for_tenant', ['company' => $tenant->name]))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <div class="admin-page-title">{{ __('messages.admin.add_user') }}</div>
        <p class="admin-page-subtitle">{{ $tenant->name }} - {{ $tenant->domain }}</p>
    </div>
    <a href="{{ route('admin.tenants.users.index', $tenant) }}" class="btn btn-outline-secondary btn-sm admin-action-label">
        <iconify-icon icon="solar:arrow-right-outline" class="me-1"></iconify-icon>
        {{ __('messages.admin.back') }}
    </a>
</div>

<div class="admin-panel p-3">
    <form action="{{ route('admin.tenants.users.store', $tenant) }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('messages.admin.name') }}</label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('messages.admin.email') }}</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('messages.admin.password') }}</label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('messages.admin.role') }}</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror">
                    <option value="">{{ __('messages.admin.no_role') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1"
                           id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_active">{{ __('messages.admin.active_user') }}</label>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-sm admin-action-label">
                <iconify-icon icon="solar:user-plus-rounded-outline" class="me-1"></iconify-icon>
                {{ __('messages.admin.add') }}
            </button>
            <a href="{{ route('admin.tenants.users.index', $tenant) }}" class="btn btn-outline-secondary">{{ __('messages.admin.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
