@extends('admin.layouts.app')

@section('title', __('messages.admin.tenant_users_title', ['company' => $tenant->name]))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <div class="admin-page-title">{{ __('messages.admin.tenant_users_title', ['company' => $tenant->name]) }}</div>
        <p class="admin-page-subtitle">{{ $tenant->domain }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.tenants.users.create', $tenant) }}" class="btn btn-primary btn-sm admin-action-label">
            <iconify-icon icon="solar:user-plus-rounded-outline" class="me-1"></iconify-icon>
            {{ __('messages.admin.add_user') }}
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm admin-action-label">
            <iconify-icon icon="solar:arrow-right-outline" class="me-1"></iconify-icon>
            {{ __('messages.admin.back') }}
        </a>
    </div>
</div>

<div class="admin-panel overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover admin-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.admin.user') }}</th>
                    <th>{{ __('messages.admin.email') }}</th>
                    <th>{{ __('messages.admin.status') }}</th>
                    <th>{{ __('messages.admin.created_at') }}</th>
                    <th>{{ __('messages.admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <small class="text-muted">ID: {{ $user->id }}</small>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->is_active ? __('messages.admin.active') : __('messages.admin.inactive') }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.tenants.users.status', [$tenant, $user]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }} btn-sm">
                                        {{ $user->is_active ? __('messages.admin.deactivate') : __('messages.admin.activate') }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.tenants.users.destroy', [$tenant, $user]) }}"
                                      method="POST"
                                      class="js-confirm-form"
                                      data-confirm-message="{{ __('messages.admin.confirm_delete_user', ['user' => $user->name]) }}">
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
                        <td colspan="6" class="text-center text-muted py-5">{{ __('messages.admin.no_users_for_tenant') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
