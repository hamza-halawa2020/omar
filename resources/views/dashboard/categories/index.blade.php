@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">

        <div class="d-flex justify-content-between mb-3">
            <div class="fw-bold fs-5">Categories</div>
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal" data-bs-target="#createModal">+ Add
                Category</button>
        </div>

        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0" id="categoriesTable">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Parent</th>
                    <th class="text-center">Created By</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data will be loaded via AJAX --}}
            </tbody>
        </table>
    </div>

    <!-- Create Modal -->
    @include('dashboard.categories.create')
    <!-- Edit Modal -->
    @include('dashboard.categories.edit')
    <!-- Delete Modal -->
    @include('dashboard.categories.delete')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadCategories();

            function loadCategories() {
                $.get("{{ route('categories.list') }}", function(res) {
                    if (res.status) {
                        let rows = '';
                        let parentOptions = '<option value="">None</option>';
                        res.data.forEach((cat, i) => {
                            rows += `
                <tr>
                    <td>${i+1}</td>
                    <td>${cat.name}</td>
                    <td>${cat.parent ? cat.parent.name : ''}</td>
                    <td>${cat.creator ? cat.creator.name : ''}</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm radius-8 editBtn" data-id="${cat.id}" data-name="${cat.name}" data-parent="${cat.parent ? cat.parent.id : ''}">Edit</button>
                        <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${cat.id}" data-name="${cat.name}">Delete</button>
                    </td>
                </tr>`;
                            parentOptions += `<option value="${cat.id}">${cat.name}</option>`;
                        });
                        $('#categoriesTable tbody').html(rows);
                        $('#parentSelect').html(parentOptions);
                        $('#editParent').html(parentOptions);
                    }
                });
            }

            // Create
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('categories.store') }}", $(this).serialize(), function(res) {
                    if (res.status) {
                        $('#createModal').modal('hide');
                        loadCategories();
                        showToast(res.message, 'success');
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

                // Set edit fields
                $('#editId').val(id);
                $('#editName').val(name);
                $.get("{{ route('categories.list') }}", function(res) {
                    if (res.status) {
                        let parentOptions = '<option value="">None</option>';
                        res.data.forEach(cat => {
                            if (cat.id != id) { 
                                parentOptions +=`<option value="${cat.id}">${cat.name}</option>`;
                            }
                        });

                        $('#editParent').html(parentOptions);
                        $('#editParent').val($(this).data('parent') ?? '');
                        $('#editModal').modal('show');
                    }
                });
            });


            // Update
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#editId').val();
                $.ajax({
                    url: "/dashboard/categories/" + id,
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#editModal').modal('hide');
                            loadCategories();
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
                    url: "/dashboard/categories/" + id,
                    type: "DELETE",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#deleteModal').modal('hide');
                            loadCategories();
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
