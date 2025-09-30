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
                       
                                <span class="badge bg-primary p-1">{{ $user->roles->first()->name  ?? ''}}</span>
                      
                        </td>
                        <td>
                            <button class="btn btn-outline-primary btn-sm editUserBtn" data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                data-roles="{{ $user->roles->first()->name ?? '' }}">
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
        $(document).ready(function() {

            // ============ Create ============
            $('#saveUserBtn').click(function(e) {
                e.preventDefault();
                let formData = $('#createUserForm').serialize();
                

                $.ajax({
                    url: "{{ route('users.store') }}",
                    method: "POST",
                    data: formData,
                    beforeSend: function() {
                        $('.error-text').text('');
                        $('#saveUserBtn').prop('disabled', true).text(
                            "{{ __('messages.saving') }}...");
                    },
                    success: function(response) {
                        if (response.status) {
                            showToast(response.message, 'success');

                            $('#createUserForm')[0].reset();
                            $('#createUserModal').modal('hide');

                            // Append new row
                            let rolesHtml = '';
                            response.data.roles.forEach(role => {
                                rolesHtml +=
                                    `<span class="badge bg-primary p-1">${role.name}</span> `;
                            });

                            $('#usersTableBody').prepend(`
                            <tr data-id="${response.data.id}">
                                <td>${response.data.name}</td>
                                <td>${response.data.email}</td>
                                <td>${rolesHtml}</td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm editUserBtn" 
                                        data-id="${response.data.id}" 
                                        data-name="${response.data.name}" 
                                        data-email="${response.data.email}" 
                                        data-roles="${response.data.roles.map(r=>r.name).join(',')}">
                                        {{ __('messages.edit') }}
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm deleteUserBtn" 
                                        data-id="${response.data.id}" 
                                        data-name="${response.data.name}">
                                        {{ __('messages.delete') }}
                                    </button>
                                </td>
                            </tr>
                        `);
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                $(`.${key}_error`).text(val[0]);
                            });
                        } else {
                            showToast("{{ __('messages.something_went_wrong') }}", 'error');
                        }
                    },
                    complete: function() {
                        $('#saveUserBtn').prop('disabled', false).text(
                            "{{ __('messages.save') }}");
                    }
                });
            });

            // ============ Update ============
            $('#updateUserBtn').click(function(e) {
                e.preventDefault();
                let id = $('#editUserId').val();
                let formData = $('#editUserForm').serialize();

                $.ajax({
                    url: "/dashboard/users/" + id,
                    method: "PUT",
                    data: formData,
                    beforeSend: function() {
                        $('.error-text').text('');
                        $('#updateUserBtn').prop('disabled', true).text(
                            "{{ __('messages.updating') }}...");
                    },
                    success: function(response) {
                        if (response.status) {
                            showToast(response.message, 'success');
                            $('#editUserModal').modal('hide');

                            let row = $(`#usersTableBody tr[data-id="${id}"]`);
                            row.find('td:eq(0)').text(response.data.name);
                            row.find('td:eq(1)').text(response.data.email);

                            let rolesHtml = '';
                            response.data.roles.forEach(role => {
                                rolesHtml +=
                                    `<span class="badge bg-primary p-1">${role.name}</span> `;
                            });
                            row.find('td:eq(2)').html(rolesHtml);

                            row.find('.editUserBtn').data('name', response.data.name)
                                .data('email', response.data.email)
                                .data('roles', response.data.roles.map(r => r.name).join(','));
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                $(`.${key}_error`).text(val[0]);
                            });
                        } else {
                            showToast("{{ __('messages.something_went_wrong') }}", 'error');
                        }
                    },
                    complete: function() {
                        $('#updateUserBtn').prop('disabled', false).text(
                            "{{ __('messages.update') }}");
                    }
                });
            });

            // ============ Delete ============
            $('#confirmDeleteBtn').click(function(e) {
                e.preventDefault();
                let id = $('#deleteUserId').val();

                $.ajax({
                    url: "/dashboard/users/" + id,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $('#confirmDeleteBtn').prop('disabled', true).text(
                            "{{ __('messages.deleting') }}...");
                    },
                    success: function(response) {
                        if (response.status) {
                            showToast(response.message, 'success');
                            $('#deleteUserModal').modal('hide');
                            $(`#usersTableBody tr[data-id="${id}"]`).remove();
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast("{{ __('messages.something_went_wrong') }}", 'error');
                    },
                    complete: function() {
                        $('#confirmDeleteBtn').prop('disabled', false).text(
                            "{{ __('messages.delete') }}");
                    }
                });
            });

            // ================== Event delegation ==================
            $(document).on('click', '.editUserBtn', function() {
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

            $(document).on('click', '.deleteUserBtn', function() {
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
