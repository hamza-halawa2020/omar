@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <div class="fw-bold fs-5">{{ __('messages.payment_ways') }}</div>
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal" data-bs-target="#createModal">+
                {{ __('messages.create_payment_way') }}</button>
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

            $(document).on('click', '.receiveBtn, .sendBtn', function() {
                $('#receiveForm input[name="payment_way_id"], #receiveForm input[name="type"]').remove();
                let type = $(this).hasClass('receiveBtn') ? 'receive' : 'send';
                let paymentWayId = $(this).data('id');

                $('#receiveForm').append(`
                    <input type="hidden" name="payment_way_id" value="${paymentWayId}">
                    <input type="hidden" name="type" value="${type}">
                `);

                // ترجمة عنوان المودال بناءً على نوع المعاملة
                $('#transactionModal .modal-title').text(type === 'receive' ?
                    '{{ __('messages.create_receive_transaction') }}' :
                    '{{ __('messages.create_send_transaction') }}');

                $('#transactionModal').modal('show');
            });

            $('#createCategorySelect').on('change', function() {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: "{{ url('dashboard/sub-categories') }}/" + categoryId,
                        type: 'GET',
                        success: function(res) {
                            $('#createSubCategorySelect').html(
                                '<option value="">{{ __('messages.select_sub_category') }}</option>'
                            );
                            res.forEach(function(sub) {
                                $('#createSubCategorySelect').append(
                                    `<option value="${sub.id}">${sub.name}</option>`
                                );
                            });
                        },
                        error: function(err) {
                            console.error(err);
                            showToast('{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    });
                } else {
                    $('#createSubCategorySelect').html(
                        '<option value="">{{ __('messages.select_sub_category') }}</option>');
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
                                '<option value="">{{ __('messages.select_sub_category') }}</option>'
                                );
                            res.forEach(function(sub) {
                                $('#editSubCategorySelect').append(
                                    `<option value="${sub.id}">${sub.name}</option>`
                                );
                            });
                        },
                        error: function(err) {
                            console.error(err);
                            showToast('{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    });
                } else {
                    $('#editSubCategorySelect').html(
                        '<option value="">{{ __('messages.select_sub_category') }}</option>');
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
                            showToast('{{ __('messages.transaction_created_successfully') }}',
                                'success');
                            loadPaymentWays();
                        }
                    },
                    error: function(err) {
                        console.error(err.responseText);
                        showToast(
                            `{{ __('messages.something_went_wrong') }}: ${err.responseText}`,
                            'error');
                    }
                });
            });

            loadPaymentWays();

            function loadPaymentWays() {
                $.get("{{ route('payment_ways.list') }}", function(res) {
                    if (res.status) {
                        let cards = '';
                        res.data.forEach((way, i) => {
                            let categoryId = way.category_id || (way.category ? way.category.id :
                                '');
                            let subCategoryId = way.sub_category_id || (way.sub_category ? way
                                .sub_category.id : '');

                            cards += `
                                <div class="col-md-3">
                                    <div class="card shadow-sm h-100 rounded-3">
                                        <div class="d-flex justify-content-center gap-3 m-3">
                                            <button class="btn btn-outline-success btn-sm radius-8 receiveBtn" data-id="${way.id}" data-name="${way.name}">{{ __('messages.receive') }}</button>
                                            <button class="btn btn-outline-primary btn-sm radius-8 sendBtn" data-id="${way.id}" data-name="${way.name}">{{ __('messages.send') }}</button>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-title fw-bold fs-5">${way.name}</div>
                                            <p class="card-text mb-1"><strong>{{ __('messages.type') }}:</strong> ${way.type ?? ''}</p>
                                            
                                            <p class="card-text mb-1"><strong>{{ __('messages.phone_number') }}:</strong> ${way.phone_number ?? ''}</p>
                                            <p class="card-text mb-1"><strong>{{ __('messages.send_limit') }}:</strong> ${way.send_limit ?? 0}</p>
                                            <p class="card-text mb-1"><strong>{{ __('messages.send_limit_alert') }}:</strong> ${way.send_limit_alert ?? 0}</p>
                                            <p class="card-text mb-1"><strong>{{ __('messages.receive_limit') }}:</strong> ${way.receive_limit ?? 0}</p>
                                            <p class="card-text mb-1"><strong>{{ __('messages.receive_limit_alert') }}:</strong> ${way.receive_limit_alert ?? 0}</p>
                                            <p class="card-text mb-1"><strong>{{ __('messages.balance') }}:</strong> ${way.balance ?? 0}</p>
                                            <p class="card-text"><small class="">{{ __('messages.created_by') }}: ${way.creator ? way.creator.name : ''}</small></p>
                                        </div>
                                        <div class="card-footer d-flex justify-content-between">
                                            <a href="payment-ways/show/${way.id}" class="btn btn-outline-success btn-sm radius-8 text-center">{{ __('messages.details') }}</a>
                                            <button class="btn btn-outline-primary btn-sm radius-8 editBtn" 
                                                data-id="${way.id}" 
                                                data-name="${way.name}" 
                                                data-type="${way.type}" 
                                                data-phone="${way.phone_number ?? ''}" 
                                                data-receive-limit="${way.receive_limit ?? 0}" 
                                                data-send-limit="${way.send_limit ?? 0}" 
                                                data-balance="${way.balance ?? 0}" 
                                                data-category-id="${categoryId}" 
                                                data-sub-category-id="${subCategoryId}">{{ __('messages.edit') }}</button>
                                            <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${way.id}" data-name="${way.name}">{{ __('messages.delete') }}</button>
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
                        showToast('{{ __('messages.payment_way_created_successfully') }}',
                            'success');
                    } else {
                        showToast(res.message || '{{ __('messages.something_went_wrong') }}',
                            'error');
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
                $('#editCategorySelect').val($(this).data('categoryId'));

                let categoryId = $(this).data('categoryId');
                let subCategoryId = $(this).data('subCategoryId');
                if (categoryId) {
                    $.ajax({
                        url: "{{ url('dashboard/sub-categories') }}/" + categoryId,
                        type: 'GET',
                        success: function(res) {
                            $('#editSubCategorySelect').html(
                                '<option value="">{{ __('messages.select_sub_category') }}</option>'
                                );
                            res.forEach(function(sub) {
                                $('#editSubCategorySelect').append(
                                    `<option value="${sub.id}" ${sub.id == subCategoryId ? 'selected' : ''}>${sub.name}</option>`
                                );
                            });
                        },
                        error: function(err) {
                            console.error(err);
                            showToast('{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    });
                } else {
                    $('#editSubCategorySelect').html(
                        '<option value="">{{ __('messages.select_sub_category') }}</option>');
                }

                toggleFields($(this).data('type'), '.phone_limit_group_edit');
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
                            showToast('{{ __('messages.payment_way_updated_successfully') }}',
                                'success');
                        } else {
                            showToast(res.message ||
                                '{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    },
                    error: function(err) {
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
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
                            showToast('{{ __('messages.payment_way_deleted_successfully') }}',
                                'success');
                        } else {
                            showToast(res.message ||
                                '{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    },
                    error: function(err) {
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            });
        });
    </script>
@endpush
