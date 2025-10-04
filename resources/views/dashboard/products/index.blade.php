@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <div class="fw-bold fs-5">{{ __('messages.products') }}</div>
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal"
                data-bs-target="#createModal">{{ __('messages.add_product') }}</button>
        </div>

        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0" id="productsTable">
            <thead>
                <tr>
                    <th class="text-center">{{ __('messages.id') }}</th>
                    <th class="text-center">{{ __('messages.name') }}</th>
                    <th class="text-center">{{ __('messages.description') }}</th>
                    <th class="text-center">{{ __('messages.purchase_price') }}</th>
                    <th class="text-center">{{ __('messages.sale_price') }}</th>
                    <th class="text-center">{{ __('messages.stock') }}</th>
                    <th class="text-center">{{ __('messages.created_by') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data will be loaded via AJAX --}}
            </tbody>
        </table>
    </div>

    <!-- Create Modal -->
    @include('dashboard.products.create')
    <!-- Edit Modal -->
    @include('dashboard.products.edit')
    <!-- Delete Modal -->
    @include('dashboard.products.delete')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadproducts();

            function loadproducts() {
                $.get("{{ route('products.list') }}", function(res) {
                    if (res.status) {
                        let rows = '';
                        let parentOptions = '<option value="">{{ __('messages.none') }}</option>';
                        res.data.forEach((cat, i) => {
                            rows += `
                <tr>
                    <td>${i+1}</td>
                    <td>${cat.name}</td>
                    <td>${cat.description}</td>
                    <td class="purchase-price" 
                        data-real="${cat.purchase_price}" 
                        data-fake="${cat.sale_price}" 
                        data-state="fake">
                        ${cat.sale_price}
                    </td>
                                    <td>${cat.sale_price}</td>
                    <td>${cat.stock}</td>
                    <td>${cat.creator ? cat.creator.name : ''}</td>
                    <td>
                        <a href="/dashboard/products/${cat.id}/details" class="btn btn-outline-success btn-sm radius-8">{{ __('messages.details') }}</a>
                        <button class="btn btn-outline-primary btn-sm radius-8 editBtn" 
                        data-id="${cat.id}" 
                        data-name="${cat.name}" 
                        data-description="${cat.description}" 
                        data-purchase_price="${cat.purchase_price}" 
                        data-sale_price="${cat.sale_price}" 
                        data-stock="${cat.stock}">{{ __('messages.edit') }}</button>
                        <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${cat.id}" data-name="${cat.name}">{{ __('messages.delete') }}</button>

                    </td>
                </tr>`;
                            parentOptions += `<option value="${cat.id}">${cat.name}</option>`;
                        });
                        $('#productsTable tbody').html(rows);
                        $('#parentSelect').html(parentOptions);
                        $('#editParent').html(parentOptions);
                    }
                });
            }

            // Create
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('products.store') }}", $(this).serialize(), function(res) {
                    if (res.status) {
                        $('#createModal').modal('hide');
                        loadproducts();
                        showToast(res.message, 'success');
                        $('#createForm')[0].reset();
                    } else {
                        $('#createModal').modal('hide');
                        showToast(res.message, 'error');
                    }
                });
            });

            $(document).on('click', '.purchase-price', function() {
                let state = $(this).data('state');
                let realPrice = $(this).data('real');
                let fakePrice = $(this).data('fake');

                if (state === 'fake') {
                    $(this).text(realPrice).data('state', 'real');
                } else {
                    $(this).text(fakePrice).data('state', 'fake');
                }
            });




            // Edit (open modal)
            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let description = $(this).data('description');
                let purchase_price = $(this).data('purchase_price');
                let sale_price = $(this).data('sale_price');
                let stock = $(this).data('stock');


                $('#editId').val(id);
                $('#editName').val(name);
                $('#editDescription').val(description);
                $('#editPurchasePrice').val(purchase_price);
                $('#editSalePrice').val(sale_price);
                $('#editStock').val(stock);

                $.get("{{ route('products.list') }}", function(res) {
                    if (res.status) {
                        $('#editModal').modal('show');
                    }
                });
            });



            // Update
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#editId').val();
                $.ajax({
                    url: "/dashboard/products/" + id,
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#editModal').modal('hide');
                            loadproducts();
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
                    url: "/dashboard/products/" + id,
                    type: "DELETE",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#deleteModal').modal('hide');
                            loadproducts();
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
