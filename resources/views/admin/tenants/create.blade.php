@extends('admin.layouts.app')

@section('title', __('messages.admin.create_tenant'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <div class="admin-page-title">{{ __('messages.admin.create_tenant') }}</div>
        <p class="admin-page-subtitle">{{ __('messages.admin.create_tenant_subtitle') }}</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm admin-action-label">
        <iconify-icon icon="solar:arrow-right-outline" class="me-1"></iconify-icon>
        {{ __('messages.admin.back') }}
    </a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="admin-panel p-3">
            <form action="{{ route('admin.tenants.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('messages.admin.company_name') }}</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('messages.admin.domain') }}</label>
                    <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror"
                           value="{{ old('domain') }}" placeholder="example.com" required>
                    @error('domain')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm admin-action-label">
                        <iconify-icon icon="solar:add-circle-outline" class="me-1"></iconify-icon>
                        {{ __('messages.admin.create') }}
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">{{ __('messages.admin.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="admin-panel p-3 h-100">
            <div class="metric-icon mb-3">
                <iconify-icon icon="solar:database-outline"></iconify-icon>
            </div>
            <div class="fw-bold">{{ __('messages.admin.after_create_tenant_title') }}</div>
            <p class="text-muted mb-3">{{ __('messages.admin.after_create_tenant_text') }}</p>
            <div class="d-grid gap-2">
                <div class="d-flex align-items-center gap-2 text-muted">
                    <iconify-icon icon="solar:check-circle-outline" class="text-success"></iconify-icon>
                    {{ __('messages.admin.tenant_database_note') }}
                </div>
                <div class="d-flex align-items-center gap-2 text-muted">
                    <iconify-icon icon="solar:check-circle-outline" class="text-success"></iconify-icon>
                    {{ __('messages.admin.tenant_roles_note') }}
                </div>
                <div class="d-flex align-items-center gap-2 text-muted">
                    <iconify-icon icon="solar:check-circle-outline" class="text-success"></iconify-icon>
                    {{ __('messages.admin.central_users_note') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
