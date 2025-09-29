@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>{{ __('messages.users') }}</div>
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal" data-bs-target="#createUserModal">
                {{ __('messages.create_user') }}
            </button>
        </div>

            <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
            <thead>
                <tr>
                    <th class="text-center">{{ __('messages.name') }}</th>
                    <th class="text-center">{{ __('messages.email') }}</th>
                    <th class="text-center">{{ __('messages.roles') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                @foreach ($users as $user)
                    <tr data-id="{{ $user->id }}">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-primary p-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm editUserBtn" data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                data-roles="{{ $user->roles->pluck('name')->join(',') }}">
                                {{ __('messages.edit') }}
                            </button>
                            <button class="btn btn-outline-danger btn-sm deleteUserBtn" data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}">
                                {{ __('messages.delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('dashboard.users.create')
    @include('dashboard.users.edit')
    @include('dashboard.users.delete')

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fill Edit Modal
            $('.editUserBtn').on('click', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let email = $(this).data('email');
                let roles = $(this).data('roles').split(',');

                $('#editUserId').val(id);
                $('#editUserName').val(name);
                $('#editUserEmail').val(email);
                $('#editUserForm').attr('action', '/dashboard/users/' + id);

                $('#editUserRoles option').each(function() {
                    $(this).prop('selected', roles.includes($(this).val()));
                });

                $('#editUserModal').modal('show');
            });

            // Fill Delete Modal
            $('.deleteUserBtn').on('click', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');

                $('#deleteUserId').val(id);
                $('#deleteUserName').text(name);
                $('#deleteUserForm').attr('action', '/dashboard/users/' + id);

                $('#deleteUserModal').modal('show');
            });
        });
    </script>
@endpush
