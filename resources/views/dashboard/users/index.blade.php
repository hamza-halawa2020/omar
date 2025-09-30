@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>{{ __('messages.users') }}</div>
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal" data-bs-target="#createUserModal">
                {{ __('messages.create_user') }}
            </button>
        </div>

        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0" id="usersTable">
            <thead>
                <tr>
                    <th class="text-center">{{ __('messages.id') }}</th>
                    <th class="text-center">{{ __('messages.name') }}</th>
                    <th class="text-center">{{ __('messages.email') }}</th>
                    <th class="text-center">{{ __('messages.roles') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data will be loaded via AJAX --}}

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

            loadUsers();

            function loadUsers() {
                $.get("{{ route('users.list') }}", function(res) {
                    if (res.status) {
                        let rows = '';
                        res.data.forEach((user, i) => {
                            rows += `
                                <tr data-id="${user.id}">
                                    <td>${i+1}</td>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>${user.roles.join(', ')}</td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm radius-8 editUserBtn" 
                                            data-id="${user.id}" 
                                            data-name="${user.name}" 
                                            data-email="${user.email}" 
                                            data-roles='${JSON.stringify(user.roles)}'>
                                            {{ __('messages.edit') }}
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm radius-8 deleteUserBtn" 
                                            data-id="${user.id}" 
                                            data-name="${user.name}">
                                            {{ __('messages.delete') }}
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#usersTable tbody').html(rows);
                    }
                });
            }


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
                            loadUsers();
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
                            loadUsers();
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
                            loadUsers();
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
                let roles = $(this).data('roles');

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
