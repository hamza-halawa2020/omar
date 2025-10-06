@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>{{ __('messages.roles') }}</div>
            @can('roles_store')
                <a href="{{ route('roles.create') }}" class="btn btn-outline-primary btn-sm radius-8">
                    {{ __('messages.create_role') }}
                </a>
            @endcan
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div style="overflow: auto">
            <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0" id="categoriesTable">

                <thead>
                    <tr>
                        <th class="text-center">{{ __('messages.name') }}</th>
                        <th class="text-center">{{ __('messages.permissions') }}</th>
                        @canany(['roles_update', 'roles_destroy'])
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td class="" style="max-width: 250px;">
                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                    @foreach ($role->permissions as $permission)
                                        <span
                                            class="badge bg-primary px-3 py-1">{{ __('messages.' . $permission->name) }}</span>
                                    @endforeach
                                </div>
                            </td>

                            @canany(['roles_update', 'roles_destroy'])
                                <td>
                                    @can('roles_update')
                                        <a href="{{ route('roles.edit', $role->id) }}"
                                            class="btn btn-outline-primary btn-sm radius-8">{{ __('messages.edit') }}</a>
                                    @endcan
                                    @can('roles_destroy')
                                        <button type="button" class="btn btn-outline-danger btn-sm radius-8" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal" data-id="{{ $role->id }}"
                                            data-name="{{ $role->name }}">
                                            {{ __('messages.delete') }}
                                        </button>
                                    @endcan
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $roles->links() }}
    </div>

    @include('dashboard.roles.delete')
@endsection

@push('scripts')
    <script>
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            let button = event.relatedTarget;
            let id = button.getAttribute('data-id');
            let name = button.getAttribute('data-name');
            let deleteForm = document.getElementById('deleteForm');
            let deleteName = document.getElementById('deleteName');
            deleteName.textContent = name;
            deleteForm.action = "/dashboard/roles/" + id;
        });
    </script>
@endpush
