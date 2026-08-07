@extends('admin.layouts.app')

@section('title', __('messages.admin.admin_dashboard'))

@section('content')
@php
    $totalUsers = $tenants->sum('users_count');
    $activeUsers = $tenants->sum('active_users_count');
    $inactiveUsers = max($totalUsers - $activeUsers, 0);
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <div class="admin-page-title">{{ __('messages.admin.tenants') }}</div>
        <p class="admin-page-subtitle">{{ __('messages.admin.tenants_subtitle') }}</p>
    </div>
    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm admin-action-label">
        <iconify-icon icon="mingcute:add-line" class="me-1"></iconify-icon>
        {{ __('messages.admin.add_tenant') }}
    </a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted small mb-1">{{ __('messages.admin.tenants') }}</p>
                    <div class="fw-bold mb-0">{{ $tenants->count() }}</div>
                </div>
                <span class="metric-icon">
                    <iconify-icon icon="solar:buildings-2-outline"></iconify-icon>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted small mb-1">{{ __('messages.admin.active') }}</p>
                    <div class="fw-bold mb-0">{{ $activeUsers }}</div>
                </div>
                <span class="metric-icon" style="background: var(--admin-success-soft); color: #16803d;">
                    <iconify-icon icon="solar:user-check-rounded-outline"></iconify-icon>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted small mb-1">{{ __('messages.admin.inactive') }}</p>
                    <div class="fw-bold mb-0">{{ $inactiveUsers }}</div>
                </div>
                <span class="metric-icon" style="background: var(--admin-warning-soft); color: #b7791f;">
                    <iconify-icon icon="solar:user-block-rounded-outline"></iconify-icon>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="admin-panel overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-bottom">
        <div>
            <div class="fw-bold mb-1">{{ __('messages.admin.tenants_list') }}</div>
            <small class="text-muted">{{ __('messages.admin.tenants_list_subtitle') }}</small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover admin-table mb-0">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">{{ __('messages.admin.company') }}</th>
                    <th class="text-center">{{ __('messages.admin.domain') }}</th>
                    <th class="text-center">{{ __('messages.admin.users') }}</th>
                    <th class="text-center">{{ __('messages.admin.active') }}</th>
                    <th class="text-center">{{ __('messages.admin.created_at') }}</th>
                    <th class="text-center">{{ __('messages.admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tenants as $tenant)
                    <tr class="text-center">
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold">{{ $tenant->name }}</div>
                            <small class="text-muted">{{ $tenant->database()->getName() }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $tenant->domain }}</span></td>
                        <td>{{ $tenant->users_count }}</td>
                        <td>
                            <span class="badge bg-success">{{ $tenant->active_users_count }}</span>
                        </td>
                        <td>{{ $tenant->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admin.tenants.roles.index', $tenant) }}"
                                   class="btn btn-outline-primary btn-sm admin-icon-btn" title="{{ __('messages.admin.roles') }}">
                                    <iconify-icon icon="solar:shield-keyhole-outline"></iconify-icon>
                                </a>
                                <a href="{{ route('admin.tenants.users.index', $tenant) }}"
                                   class="btn btn-outline-secondary btn-sm admin-icon-btn" title="{{ __('messages.admin.users') }}">
                                    <iconify-icon icon="solar:users-group-rounded-outline"></iconify-icon>
                                </a>
                                <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST"
                                      class="js-confirm-form"
                                      data-confirm-message="{{ __('messages.admin.confirm_delete_tenant') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm admin-icon-btn" title="{{ __('messages.admin.delete') }}">
                                        <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">{{ __('messages.admin.no_tenants') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
