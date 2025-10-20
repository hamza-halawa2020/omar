@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <div class="fw-bold fs-5">{{ __('messages.installments') }}</div>
            @can('installments_store')
                <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal"
                    data-bs-target="#createModal">{{ __('messages.add_installment') }}</button>
            @endcan
        </div>


        <div style="overflow-x:auto;">
            <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0"
                id="installmentsTable">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">{{ __('messages.client') }}</th>
                        <th class="text-center">{{ __('messages.product') }}</th>
               
                        <th class="text-center">{{ __('messages.installment_amount') }}</th>
                        <th class="text-center">{{ __('messages.down_payment') }}</th>
                        <th class="text-center">{{ __('messages.remaining_amount') }}</th>
                        <th class="text-center">{{ __('messages.installment_count_left') }}</th>
                        @canany(['installments_show', 'installments_update', 'installments_destroy'])
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        @endcan
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
    {{-- @include('dashboard.installment_contracts.pay') --}}
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

                        <td>${contract.installment_amount}</td>
                        <td>${contract.down_payment}</td>
                        <td>${contract.remaining_amount}</td>
                        <td>${contract.remaining_installments}</td>
                        @canany(['installments_show', 'installments_update', 'installments_destroy'])
                            <td>
                                @can('installments_show')
                                    <a href="{{ url('dashboard/installment_contracts') }}/${contract.id}"  class="btn btn-outline-success btn-sm radius-8">{{ __('messages.details') }}</a>
                                @endcan
                                @can('installments_update')
                                    <button 
                                        class="btn btn-outline-primary btn-sm radius-8 editBtn"
                                        data-id="${contract.id}"
                                        data-client_id="${contract.client?.id ?? ''}"
                                        data-product_id="${contract.product?.id ?? ''}"
                                        data-product_price="${contract.product?.purchase_price ?? ''}"
                                        data-down_payment="${contract.down_payment}"
                                        data-interest_rate="${contract.interest_rate ?? ''}" 
                                        data-installment_count="${contract.installment_count}"
                                        data-start_date="${contract.start_date}"
                                        data-total_amount="${contract.total_amount}"
                                        data-remaining_amount="${contract.remaining_amount}"
                                        data-remaining_installments="${contract.remaining_installments}"
                                        data-next_due_date="${contract.next_due_date}"
                                    >{{ __('messages.edit') }}</button>
                                @endcan
                                @can('installments_destroy')
                                    <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${contract.id}">{{ __('messages.delete') }}</button>  
                                @endcan
                            </td>
                        @endcan
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
                let clientId = $(this).data('client_id');
                let productId = $(this).data('product_id');
                let productPrice = $(this).data('product_price');
                let downPayment = $(this).data('down_payment');
                let interestRate = $(this).data('interest_rate');
                let installmentCount = $(this).data('installment_count');
                let startDate = $(this).data('start_date');

                // الجديد
                let totalAmount = $(this).data('total_amount');
                let remainingAmount = $(this).data('remaining_amount');
                let remainingInstallments = $(this).data('remaining_installments');
                let nextDueDate = $(this).data('next_due_date');

                $('#editId').val(id);
                $('#editClientId').val(clientId);
                $('#editProductId').val(productId);
                $('#editProductPrice').val(productPrice);
                $('#editDownPayment').val(downPayment);
                $('#editInterestRate').val(interestRate);
                $('#editInstallmentCount').val(installmentCount);
                $('#editStartDate').val(startDate);

                // الجديد
                $('#editTotalAmount').val(totalAmount);
                $('#editRemainingAmount').val(remainingAmount);
                $('#editRemainingInstallments').val(remainingInstallments);
                $('#editNextDueDate').val(nextDueDate);

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
                    url: "/dashboard/installment_contracts/" + id,
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

            $(document).on('click', '.payBtn', function() {
                let id = $(this).data('id');
                $('#payInstallmentId').val(id);
                $('#payModal').modal('show');
            });

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
