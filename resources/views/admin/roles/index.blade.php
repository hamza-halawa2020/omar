@extends('admin.layouts.app')

@section('title', __('messages.admin.manage_roles_title', ['company' => $tenant->name]))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none small">
            <iconify-icon icon="solar:arrow-right-outline"></iconify-icon> {{ __('messages.admin.home') }}
        </a>
        <div class="fw-bold mb-0 mt-1">{{ __('messages.admin.tenant_roles_title', ['company' => $tenant->name]) }}</div>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        {{-- <iconify-icon icon="mingcute:add-line" class="me-1"></iconify-icon> --}}
        {{ __('messages.admin.add_role') }}
    </button>
</div>

<div class="row g-3">
    @forelse ($roles as $role)
        <div class="col-md-6 col-lg-4">
            <div class="admin-panel h-100 p-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">{{ $role->name }}</span>
                    <form action="{{ route('admin.tenants.roles.destroy', [$tenant, $role->id]) }}" method="POST"
                          class="js-confirm-form"
                          data-confirm-message="{{ __('messages.admin.confirm_delete_role') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm admin-icon-btn">
                            <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                        </button>
                    </form>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('admin.tenants.roles.permissions', [$tenant, $role->id]) }}" method="POST">
                        @csrf
                        <div class="form-check mb-2">
                            <input class="form-check-input js-permissions-select-all" type="checkbox"
                                   id="select_all_permissions_{{ $role->id }}"
                                   data-role-id="{{ $role->id }}">
                            <label class="form-check-label small fw-semibold" for="select_all_permissions_{{ $role->id }}">
                                {{ __('messages.admin.select_all_permissions') }}
                            </label>
                        </div>
                        <div class="row row-cols-1 g-1 mb-3" style="max-height:260px; overflow:auto;">
                            @foreach ($permissions as $permission)
                                @php
                                    $permissionLabel = __('messages.' . $permission->name);

                                    if ($permissionLabel === 'messages.' . $permission->name) {
                                        $permissionLabel = str($permission->name)->replace(['_', '.'], ' ')->title();
                                    }
                                @endphp
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               id="perm_{{ $role->id }}_{{ $permission->id }}"
                                               data-role-permission="{{ $role->id }}"
                                               @checked($role->permissions->contains('name', $permission->name))>
                                        <label class="form-check-label small" for="perm_{{ $role->id }}_{{ $permission->id }}">
                                            {{ $permissionLabel }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            {{ __('messages.admin.save_permissions') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">{{ __('messages.admin.no_roles_for_tenant') }}</div>
        </div>
    @endforelse
</div>

{{-- Create Role Modal --}}
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tenants.roles.store', $tenant) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <div class="modal-title">{{ __('messages.admin.add_role') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">{{ __('messages.admin.role_name') }}</label>
                    <input type="text" name="name" class="form-control" required placeholder="{{ __('messages.admin.role_name_placeholder') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('messages.admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('messages.admin.create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            function syncSelectAllState(roleId) {
                const permissions = $(`[data-role-permission="${roleId}"]`);
                const selectAll = $(`[data-role-id="${roleId}"]`).get(0);

                if (! selectAll || permissions.length === 0) {
                    return;
                }

                const checkedCount = permissions.filter(':checked').length;
                selectAll.checked = checkedCount === permissions.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < permissions.length;
            }

            $('.js-permissions-select-all').each(function () {
                syncSelectAllState(this.dataset.roleId);
            });

            $('.js-permissions-select-all').on('change', function () {
                $(`[data-role-permission="${this.dataset.roleId}"]`).prop('checked', this.checked);
                this.indeterminate = false;
            });

            $('[data-role-permission]').on('change', function () {
                syncSelectAllState(this.dataset.rolePermission);
            });
        });
    </script>
@endpush
