@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="role-form-shell">
        <div class="d-flex justify-content-between align-items-center gap-2 mb-3 mobile-stack-header">
            <div>
                <h5 class="mb-1">{{ __('messages.edit_role') }}</h5>
                <div class="text-muted small">{{ $role->name }}</div>
            </div>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm radius-8">
                {{ __('messages.back') }}
            </a>
        </div>

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
                <input type="text" class="form-control mb-3 js-permissions-filter"
                    placeholder="{{ __('messages.search_placeholder') }}">
                <div class="row mobile-permissions-grid js-permissions-list">
                    @foreach ($permissions as $permission)
                        <div class="d-flex col-md-3 col-sm-6 mb-2 js-permission-item"
                            data-permission="{{ __('messages.' . $permission->name) }} {{ $permission->name }}">
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
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('.js-permissions-filter').on('input', function() {
                const search = this.value.toLowerCase();

                $('.js-permission-item').each(function() {
                    $(this).toggle($(this).data('permission').toLowerCase().includes(search));
                });
            });
        });
    </script>
@endpush
