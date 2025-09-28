@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <div class="fw-bold fs-5">{{ __('messages.installments') }}</div>
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal"
                data-bs-target="#createModal">{{ __('messages.add_installment') }}</button>
        </div>


        <div style="overflow-x:auto;">
            <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0"
                id="installmentsTable">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">{{ __('messages.client') }}</th>
                        <th class="text-center">{{ __('messages.product') }}</th>
                        <th class="text-center">{{ __('messages.installment_date') }}</th>
                        <th class="text-center">{{ __('messages.total_amount') }}</th>
                        <th class="text-center">{{ __('messages.installment_amount') }}</th>
                        <th class="text-center">{{ __('messages.down_payment') }}</th>
                        <th class="text-center">{{ __('messages.remaining_amount') }}</th>
                        <th class="text-center">{{ __('messages.installment_count') }}</th>
                        <th class="text-center">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Data will be loaded via AJAX --}}
                </tbody>
            </table>
        </div>

    </div>

    <!-- Create Modal -->
    @include('dashboard.installment_contracts.create')
    <!-- Edit Modal -->
    @include('dashboard.installment_contracts.edit')
    <!-- Pay Modal -->
    @include('dashboard.installment_contracts.pay')
    <!-- Delete Modal -->
    @include('dashboard.installment_contracts.delete')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadinstallments();

            function loadinstallments() {
                $.get("{{ route('installment_contracts.list') }}", function(res) {
                    if (res.status) {
                        let rows = '';
                        res.data.forEach((contract, i) => {
                            let clientName = contract.client?.name ?? '';
                            let clientPhone = contract.client?.phone_number ?? '';
                            let productName = contract.product?.name ?? '';
                            let purchasePrice = contract.product?.purchase_price ?? '';

                            rows += `
                    <tr>
                        <td>${i+1}</td>
                        <td>
                            <strong>${clientName}</strong><br>
                            <small>${clientPhone}</small>
                        </td>
                        <td>${productName}</td>
                        <td>${contract.start_date}</td>
                        <td>${contract.total_amount}</td>
                        <td>${contract.installment_amount}</td>
                        <td>${contract.down_payment}</td>
                        <td>${contract.total_amount - contract.down_payment}</td>
                        <td>${contract.installment_count}</td>
                        <td>
                            <a href="{{ url('dashboard/installment_contracts') }}/${contract.id}"  class="btn btn-outline-success btn-sm radius-8">{{ __('messages.details') }}</a>
                            <button 
                                class="btn btn-outline-primary btn-sm radius-8 editBtn"
                                data-id="${contract.id}"
                                data-name="${contract.name ?? ''}"
                                data-description="${contract.description ?? ''}"
                                data-purchase_price="${contract.product?.purchase_price ?? ''}"
                                data-sale_price="${contract.product?.sale_price ?? ''}"
                                data-stock="${contract.product?.stock ?? ''}"
                            >{{ __('messages.edit') }}</button>
                            <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${contract.id}">{{ __('messages.delete') }}</button>   
                        </td>
                    </tr>`;
                        });
                        $('#installmentsTable tbody').html(rows);
                    }
                });
            }

            // Create
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('installment_contracts.store') }}", $(this).serialize(), function(res) {
                    if (res.status) {
                        $('#createModal').modal('hide');
                        loadinstallments();
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

                $('#editModal').modal('show');
            });


            // Update
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#editId').val();

                $.ajax({
                    url: "/dashboard/installment_contracts/" + id,
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#editModal').modal('hide');
                            loadinstallments();
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
                    url: "/dashboard/installments/" + id,
                    type: "DELETE",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#deleteModal').modal('hide');
                            loadinstallments();
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

            // فتح مودال الدفع
            $(document).on('click', '.payBtn', function() {
                let id = $(this).data('id');
                $('#payInstallmentId').val(id);
                $('#payModal').modal('show');
            });

            // تنفيذ الدفع
            $('#payForm').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('installments.pay') }}", $(this).serialize(), function(res) {
                    if (res.status) {
                        $('#payModal').modal('hide');
                        loadinstallments();
                        showToast(res.message, 'success');
                    } else {
                        showToast(res.message, 'error');
                    }
                }).fail(function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                });
            });


        });
    </script>
@endpush
