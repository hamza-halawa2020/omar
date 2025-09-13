@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <div class="fw-bold fs-5">Payment Ways</div>
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal" data-bs-target="#createModal">+ Add
                Payment Way</button>
        </div>

        <div id="paymentWaysContainer" class="row g-3">
            {{-- Data via AJAX --}}
        </div>
    </div>

    <!-- Modals -->
    @include('dashboard.payment_ways.create')
    @include('dashboard.payment_ways.edit')
    @include('dashboard.payment_ways.delete')
    @include('dashboard.payment_ways.transactionModal')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            function toggleFields(type, groupClass) {
                if (type === 'wallet') {
                    $(groupClass).show();
                } else {
                    $(groupClass).hide();
                }
            }

            $('select[name="type"]').on('change', function() {
                toggleFields($(this).val(), '.phone_limit_group');
            });
            toggleFields($('select[name="type"]').val(), '.phone_limit_group');

            $('#editType').on('change', function() {
                toggleFields($(this).val(), '.phone_limit_group_edit');
            });

            $(document).on('click', '.editBtn', function() {
                let type = $(this).data('type');
                toggleFields(type, '.phone_limit_group_edit');
            });

            $(document).on('click', '.receiveBtn, .sendBtn', function() {
                $('#receiveForm input[name="payment_way_id"], #receiveForm input[name="type"]').remove();
                let type = $(this).hasClass('receiveBtn') ? 'receive' : 'send';
                let paymentWayId = $(this).data('id');

                $('#receiveForm').append(`
                    <input type="hidden" name="payment_way_id" value="${paymentWayId}">
                    <input type="hidden" name="type" value="${type}">
                `);

                $('#transactionModal .modal-title').text(type.charAt(0).toUpperCase() + type.slice(1) +
                    ' Transaction');

                $('#transactionModal').modal('show');
            });

            $('#categorySelect').on('change', function() {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: "{{ url('dashboard/sub-categories') }}/" + categoryId,
                        type: 'GET',
                        success: function(res) {
                            $('#subCategorySelect').html(
                                '<option value="">Select Sub Category</option>');
                            res.forEach(function(sub) {
                                $('#subCategorySelect').append(
                                    `<option value="${sub.id}">${sub.name}</option>`
                                );
                            });
                        },
                        error: function(err) {
                            console.error(err);
                        }
                    });
                } else {
                    $('#subCategorySelect').html('<option value="">Select Sub Category</option>');
                }
            });

            $('#editCategorySelect').on('change', function() {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: "{{ url('dashboard/sub-categories') }}/" + categoryId,
                        type: 'GET',
                        success: function(res) {
                            $('#editSubCategorySelect').html(
                                '<option value="">Select Sub Category</option>');
                            res.forEach(function(sub) {
                                $('#editSubCategorySelect').append(
                                    `<option value="${sub.id}">${sub.name}</option>`
                                );
                            });
                        }
                    });
                } else {
                    $('#editSubCategorySelect').html('<option value="">Select Sub Category</option>');
                }
            });


            $('#receiveForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('transactions.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            $('#transactionModal').modal('hide');
                            alert('Transaction saved successfully!');
                        }
                    },
                    error: function(err) {
                        console.error(err.responseText);
                        alert('Something went wrong!');
                    }
                });
            });

            loadPaymentWays();

            function loadPaymentWays() {
                $.get("{{ route('payment_ways.list') }}", function(res) {
                    if (res.status) {
                        let cards = '';
                        res.data.forEach((way, i) => {
                            cards += `
                    <div class="col-md-3">
                        <div class="card shadow-sm h-100 rounded-3">
                            <div class="d-flex justify-content-center gap-3 m-3">
                                <button class="btn btn-outline-success btn-sm radius-8 receiveBtn" data-id="${way.id}" data-name="${way.name}">Receive</button>
                                <button class="btn btn-outline-primary btn-sm radius-8 sendBtn" data-id="${way.id}" data-name="${way.name}">Send</button>
                            </div>
                            <div class="card-body">
                                <div class="card-title fw-bold fs-5">${way.name}</div>
                                <p class="card-text mb-1"><strong>Type:</strong> ${way.type}</p>
                                <p class="card-text mb-1"><strong>Phone:</strong> ${way.phone_number ?? ''}</p>
                                <p class="card-text mb-1"><strong>Send Limit:</strong> ${way.send_limit ?? 0}</p>
                                <p class="card-text mb-1"><strong>Send Limit Alert:</strong>${way.send_limit_alert ?? 0}</p>
                                <p class="card-text mb-1"><strong>Receive Limit:</strong> ${way.receive_limit ?? 0}</p>
                                <p class="card-text mb-1"><strong>Receive Limit Alert:</strong>${way.receive_limit_alert ?? 0}</p>
                                <p class="card-text mb-1"><strong>Balance:</strong> ${way.balance ?? 0}</p>
                                <p class="card-text"><small class="">Created by: ${way.creator ? way.creator.name : ''}</small></p>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <a href="payment-ways/show/${way.id}" class="btn btn-outline-success btn-sm radius-8 text-center">Details</a>
                                <button class="btn btn-outline-primary btn-sm radius-8 editBtn" data-id="${way.id}" data-name="${way.name}" data-type="${way.type}" data-phone="${way.phone_number ?? ''}" data-receive-limit="${way.receive_limit ?? 0}" data-send-limit="${way.send_limit ?? 0}" data-balance="${way.balance ?? 0}">Edit</button>
                                <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${way.id}" data-name="${way.name}">Delete</button>
                            </div>
                        </div>
                    </div>`;
                        });
                        $('#paymentWaysContainer').html(cards);
                    }
                });
            }

            // Create
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('payment_ways.store') }}", $(this).serialize(), function(res) {
                    if (res.status) {
                        $('#createModal').modal('hide');
                        loadPaymentWays();
                    }
                });
            });

            // Edit
            $(document).on('click', '.editBtn', function() {
                $('#editId').val($(this).data('id'));
                $('#editName').val($(this).data('name'));
                $('#editType').val($(this).data('type'));
                $('#editPhone').val($(this).data('phone'));
                $('#editReceiveLimit').val($(this).data('receive-limit'));
                $('#editSendLimit').val($(this).data('send-limit'));
                $('#editBalance').val($(this).data('balance'));
                $('#editModal').modal('show');
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#editId').val();
                if ($('#editType').val() !== 'wallet') {
                    $('#editPhone').val('');
                    $('#editReceiveLimit').val('');
                    $('#editSendLimit').val('');
                }
                $.ajax({
                    url: "{{ url('dashboard/payment-ways') }}/" + id,
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#editModal').modal('hide');
                            loadPaymentWays();
                        }
                    }
                });
            });

            // Delete
            $(document).on('click', '.deleteBtn', function() {
                $('#deleteId').val($(this).data('id'));
                $('#deleteName').text($(this).data('name'));
                $('#deleteModal').modal('show');
            });

            $('#deleteForm').submit(function(e) {
                e.preventDefault();
                let id = $('#deleteId').val();
                $.ajax({
                    url: "{{ url('dashboard/payment-ways') }}/" + id,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status) {
                            $('#deleteModal').modal('hide');
                            loadPaymentWays();
                        }
                    }
                });
            });

        });
    </script>
@endpush
