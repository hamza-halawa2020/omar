@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <div class="fw-bold fs-5">{{ __('messages.clients') }}</div>
            @can('clients_store')
                <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal"
                    data-bs-target="#createModal">{{ __('messages.add_client') }}</button>
            @endcan
        </div>

        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0" id="clientsTable">
            <thead>
                <tr>
                    <th class="text-center">{{ __('messages.id') }}</th>
                    <th class="text-center">{{ __('messages.name') }}</th>
                    <th class="text-center">{{ __('messages.phone_number') }}</th>
                    <th class="text-center">{{ __('messages.debt') }}</th>
                    <th class="text-center">{{ __('messages.created_by') }}</th>
                    @canany(['clients_show', 'clients_update', 'clients_destroy'])   
                        <th class="text-center">{{ __('messages.actions') }}</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                {{-- Data will be loaded via AJAX --}}
            </tbody>
        </table>
    </div>

    <!-- Create Modal -->
    @include('dashboard.clients.create')
    <!-- Edit Modal -->
    @include('dashboard.clients.edit')
    <!-- Delete Modal -->
    @include('dashboard.clients.delete')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadclients();

            function loadclients() {
                $.get("{{ route('clients.list') }}", function(res) {
                    if (res.status) {
                        let rows = '';
                        res.data.forEach((client, i) => {
                            rows += `
                            <tr>
                                <td>${i+1}</td>
                                <td>${client.name}</td>
                                <td>${client.phone_number}</td>
                                <td>${client.debt}</td>
                                <td>${client.creator ? client.creator.name : ''}</td>
                                @canany(['clients_show', 'clients_update', 'clients_destroy'])   
                                    <td>
                                        @can('clients_show')   
                                            <a href="{{ url('dashboard/clients') }}/${client.id}" class="btn btn-outline-success btn-sm radius-8 btn-sm">{{ __('messages.details') }}</a>
                                        @endcan
                                        @can('clients_update')
                                            <button class="btn btn-outline-primary btn-sm radius-8 editBtn"data-id="${client.id}"data-name="${client.name}"data-phone_number="${client.phone_number}"data-debt="${client.debt}">{{ __('messages.edit') }}</button>
                                        @endcan
                                        @can('clients_destroy')
                                            <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${client.id}" data-name="${client.name}">{{ __('messages.delete') }}</button>
                                        @endcan
                                    </td>
                                @endcan
                            </tr>`;
                        });
                        $('#clientsTable tbody').html(rows);
                    }
                });
            }

            // Create
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('clients.store') }}", $(this).serialize(), function(res) {
                    if (res.status) {
                        $('#createModal').modal('hide');
                        loadclients();
                        showToast(res.message, 'success');
                        $('#createForm')[0].reset();
                    } else {
                        $('#createModal').modal('hide');
                        showToast(res.message, 'error');
                    }
                });
            });


            // Edit (open modal)
            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let phone_number = $(this).data('phone_number');
                let debt = $(this).data('debt');

                $('#editId').val(id);
                $('#editName').val(name);
                $('#editPhoneNumber').val(phone_number);
                $('#editDebt').val(debt);

                $('#editModal').modal('show');

            });



            // Update
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#editId').val();
                $.ajax({
                    url: "/dashboard/clients/" + id,
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#editModal').modal('hide');
                            loadclients();
                            showToast(res.message, 'success');
                        } else {
                            $('#editModal').modal('hide');
                            showToast(res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#editModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || 'Something went wrong', 'error');
                    }
                });
            });

            // Delete (open modal)
            $(document).on('click', '.deleteBtn', function() {
                $('#deleteId').val($(this).data('id'));
                $('#deleteName').text($(this).data('name'));
                $('#deleteModal').modal('show');
            });

            // Confirm Delete
            $('#deleteForm').submit(function(e) {
                e.preventDefault();
                let id = $('#deleteId').val();
                $.ajax({
                    url: "/dashboard/clients/" + id,
                    type: "DELETE",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#deleteModal').modal('hide');
                            loadclients();
                            showToast(res.message, 'success');
                        } else {
                            $('#deleteModal').modal('hide');
                            showToast(res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#deleteModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || 'Something went wrong', 'error');
                    }
                });
            });

        });
    </script>
@endpush
