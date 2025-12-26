@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container-fluid px-3">
        <!-- Header Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm   rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="mb-1 fw-bold">
                                    <i class="fas fa-credit-card me-2"></i>
                                    {{ __('messages.payment_ways') }}
                                </div>
                                <p class="mb-0 opacity-75">{{ __('messages.payment_ways_management') }}</p>
                            </div>
                            @can('payment_ways_store')
                                <button class="btn btn-lg rounded-pill px-3 shadow-sm" 
                                        data-bs-toggle="modal" data-bs-target="#createModal">
                                    <i class="fas fa-plus me-2"></i>
                                    {{ __('messages.create_payment_way') }}
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-3" id="statsCards">
            <!-- Stats will be loaded via AJAX -->
        </div>

        <!-- Payment Ways Grid -->
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

@push('styles')

@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            // Add loading animation
            function showLoading() {
                $('#paymentWaysContainer').html(`
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border " role="status">
                            <span class="visually-hidden">{{ __('messages.loading_text') }}</span>
                        </div>
                        <p class="mt-3 ">{{ __('messages.loading_payment_ways') }}</p>
                    </div>
                `);
            }

            function toggleFields(type, groupClass) {
                if (type === 'wallet') {
                    $(groupClass).show();
                } else {
                    $(groupClass).hide();
                }
            }

            $('select[name="type"]').on('change', function () {
                toggleFields($(this).val(), '.phone_limit_group');
            });
            toggleFields($('select[name="type"]').val(), '.phone_limit_group');

            $('#editType').on('change', function () {
                toggleFields($(this).val(), '.phone_limit_group_edit');
            });

            function loadClients(type) {
                $.get("{{ route('clients.list') }}", { type: type }, function (res) {

                    if (res.status) {
                        let clientOptions = '<option value="">{{ __('messages.select_client') }}</option>';
                        res.data.forEach(function (client) {
                            clientOptions +=
                                `<option value="${client.id}">${client.name} ({{ __('messages.debt') }}: ${parseFloat(client.debt || 0).toFixed(2)})</option>`;
                        });
                        $('#client_id').html(clientOptions);
                    } else {
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            }
            function loadProducts() {
                $.get("{{ route('products.list') }}", function (res) {
                    if (res.status) {
                        let productOptions = '<option value="">{{ __('messages.select_product') }}</option>';
                        res.data.forEach(function (product) {
                            productOptions +=
                                `<option value="${product.id}">${product.name} ({{ __('messages.sale_price') }}: ${parseFloat(product.sale_price || 0).toFixed(2)}) ({{ __('messages.stock') }}: ${product.stock || 0})</option>`;
                        });
                        $('#product_id').html(productOptions);
                    } else {
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            }

            $(document).on('click', '.receiveBtn, .sendBtn', function () {
                // Add button loading state
                let $btn = $(this);
                let originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin me-1"></i>{{ __('messages.loading_text') }}').prop('disabled', true);
                
                setTimeout(() => {
                    $btn.html(originalText).prop('disabled', false);
                }, 500);

                $('#receiveForm input[name="payment_way_id"], #receiveForm input[name="type"]').remove();
                let type = $(this).hasClass('receiveBtn') ? 'receive' : 'send';
                let paymentWayId = $(this).data('id');

                $('#receiveForm').append(`
                        <input type="hidden" name="payment_way_id" value="${paymentWayId}">
                        <input type="hidden" name="type" value="${type}">
                    `);

                $('#transactionModal .modal-title').text(type === 'receive' ?
                    '{{ __('messages.create_receive_transaction') }}' :
                    '{{ __('messages.create_send_transaction') }}');


                let clientType = $(this).closest('.card').find('span[data-client-type]').attr('data-client-type');
                
                loadProducts();
                
                $('#transactionModal').modal('show');
                loadClients(clientType);    
                        
            });

            $('#createCategorySelect').on('change', function () {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: "{{ url('dashboard/sub-categories') }}/" + categoryId,
                        type: 'GET',
                        success: function (res) {
                            $('#createSubCategorySelect').html(
                                '<option value="">{{ __('messages.select_sub_category') }}</option>'
                            );
                            res.forEach(function (sub) {
                                $('#createSubCategorySelect').append(
                                    `<option value="${sub.id}">${sub.name}</option>`
                                );
                            });
                        },
                        error: function (err) {
                            showToast('{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    });
                } else {
                    $('#createSubCategorySelect').html(
                        '<option value="">{{ __('messages.select_sub_category') }}</option>');
                }
            });

            $('#editCategorySelect').on('change', function () {
                let categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: "{{ url('dashboard/sub-categories') }}/" + categoryId,
                        type: 'GET',
                        success: function (res) {
                            $('#editSubCategorySelect').html(
                                '<option value="">{{ __('messages.select_sub_category') }}</option>'
                            );
                            res.forEach(function (sub) {
                                $('#editSubCategorySelect').append(
                                    `<option value="${sub.id}">${sub.name}</option>`
                                );
                            });
                        },
                        error: function (err) {
                            showToast('{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    });
                } else {
                    $('#editSubCategorySelect').html(
                        '<option value="">{{ __('messages.select_sub_category') }}</option>');
                }
            });

            $('#receiveForm').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('transactions.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res.status) {
                            $('#transactionModal').modal('hide');
                            showToast('{{ __('messages.transaction_created_successfully') }}', 'success');
                            $('#receiveForm')[0].reset();
                            loadPaymentWays();
                        } else {
                            showToast(res.message || '{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    },
                    error: function (err) {
                        showToast(`{{ __('messages.something_went_wrong') }}: ${err.responseJSON?.message || err.responseText}`, 'error');
                    }
                });
            });

            // Show loading before loading payment ways
            showLoading();
            loadPaymentWays();

            function loadPaymentWays() {
                $.get("{{ route('payment_ways.list') }}", function (res) {
                    if (res.status) {
                        // Load stats first
                        loadStats(res.data);
                        
                        let cards = '';
                        res.data.sort((a, b) => (a.position || 0) - (b.position || 0));
                        res.data.forEach((way, i) => {
                            let categoryId = way.category_id || (way.category ? way.category.id : '');
                            let clientType = way.client_type;

                            let clientTypeText = '';
                            let clientTypeIcon = '';
                            let clientTypeBadge = '';
                            if (clientType === 'client') {
                                clientTypeText = "{{ __('messages.client') }}";
                                clientTypeIcon = 'fas fa-user';
                                clientTypeBadge = 'bg-info';
                            } else if (clientType === 'merchant') {
                                clientTypeText = "{{ __('messages.merchant') }}";
                                clientTypeIcon = 'fas fa-store';
                                clientTypeBadge = 'bg-warning';
                            }

                            let subCategoryId = way.sub_category_id || (way.sub_category ? way.sub_category.id : '');
                            let limits = way.monthly_limits || {};
                            let monthName = limits.month_name || '';
                            
                            const typeTranslations = {
                                wallet: "{{ __('messages.wallet') }}",
                                cash: "{{ __('messages.cash') }}",
                                balance_machine: "{{ __('messages.balance_machine') }}"
                            };

                            const typeIcons = {
                                wallet: 'fas fa-wallet',
                                cash: 'fas fa-money-bill-wave',
                                balance_machine: 'fas fa-credit-card'
                            };

                            const typeColors = {
                                wallet: 'primary',
                                cash: 'success',
                                balance_machine: 'info'
                            };

                            let balance = parseFloat(way.balance ?? 0);
                            let balanceColor = balance >= 0 ? 'text-success' : 'text-danger';
                            let balanceIcon = balance >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down';

                            cards += `
                                <div class="col-xl-3 col-lg-6 col-md-6" data-id="${way.id}">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 payment-way-card" style="transition: all 0.3s ease;">
                                        <!-- Card Header -->
                                        <div class="card-header border-0 rounded-top-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="${typeIcons[way.type] || 'fas fa-credit-card'} me-2 fs-5"></i>
                                                    <div class="mb-0 fw-bold">${way.name}</div>
                                                </div>
                                                <span class="badge ${clientTypeBadge} rounded-pill">
                                                    <i class="${clientTypeIcon} me-1"></i>
                                                    ${clientTypeText}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="opacity-75">
                                                    <i class="fas fa-tag me-1"></i>
                                                    ${way.type ? (typeTranslations[way.type] || way.type) : ''}
                                                </small>
                                                <span data-client-type="${clientType}" style="display: none;"></span>
                                            </div>
                                        </div>

                                        <!-- Card Body -->
                                        <div class="card-body p-3">
                                            <!-- Balance Display -->
                                            <div class="text-center mb-3">
                                                <div class="balance-display p-3 rounded-3">
                                                    <small class=" d-block mb-1">{{ __('messages.balance') }}</small>
                                                    <div class="mb-0 fw-bold ${balanceColor}">
                                                        <i class="${balanceIcon} me-1"></i>
                                                        ${balance.toFixed(2)}
                                                    </div>
                                                </div>
                                            </div>

                                            ${way.type === 'wallet' ? `
                                                <!-- Limits Section -->
                                                <div class="limits-section">
                                                    <div class="fw-bold mb-3">
                                                        <i class="fas fa-chart-line me-2"></i>
                                                        {{ __('messages.monthly_limits') }}
                                                    </div>
                                                    
                                                    <!-- Send Limit -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-semibold ">
                                                                <i class="fas fa-paper-plane me-1"></i>
                                                                {{ __('messages.send') }}
                                                            </span>
                                                            <span class="badge  ">
                                                                ${limits.send_used || 0} / ${limits.send_limit || way.send_limit}
                                                            </span>
                                                        </div>
                                                        <div class="progress rounded-pill" style="height: 8px;">
                                                            <div class="progress-bar ${((limits.send_used || 0) / (limits.send_limit || way.send_limit || 1) * 100) >= 80 ? 'bg-danger' : 'bg-primary'} rounded-pill"
                                                                role="progressbar"
                                                                style="width: ${Math.min(((limits.send_used || 0) / (limits.send_limit || way.send_limit || 1) * 100), 100).toFixed(0)}%">
                                                            </div>
                                                        </div>
                                                        <small class="">
                                                            {{ __('messages.remaining_text') }}: ${limits.send_remaining || (way.send_limit - (limits.send_used || 0))}
                                                        </small>
                                                    </div>

                                                    <!-- Receive Limit -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-semibold text-success">
                                                                <i class="fas fa-download me-1"></i>
                                                                {{ __('messages.receive') }}
                                                            </span>
                                                            <span class="badge bg-success-subtle text-success">
                                                                ${limits.receive_used || 0} / ${limits.receive_limit || way.receive_limit}
                                                            </span>
                                                        </div>
                                                        <div class="progress rounded-pill" style="height: 8px;">
                                                            <div class="progress-bar ${((limits.receive_used || 0) / (limits.receive_limit || way.receive_limit || 1) * 100) >= 80 ? 'bg-danger' : 'bg-success'} rounded-pill"
                                                                role="progressbar"
                                                                style="width: ${Math.min(((limits.receive_used || 0) / (limits.receive_limit || way.receive_limit || 1) * 100), 100).toFixed(0)}%">
                                                            </div>
                                                        </div>
                                                        <small class="">
                                                            {{ __('messages.remaining_text') }}: ${limits.receive_remaining || (way.receive_limit - (limits.receive_used || 0))}
                                                        </small>
                                                    </div>
                                                </div>
                                            ` : ''}

                                            <!-- Creator Info -->
                                            <div class="creator-info mt-3 pt-3 border-top">
                                                <small class="">
                                                    <i class="fas fa-user-plus me-1"></i>
                                                    {{ __('messages.created_by') }}: 
                                                    <span class="fw-semibold">${way.creator ? way.creator.name : '{{ __('messages.not_specified') }}'}</span>
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        @can('transactions_store')
                                        <div class="card-body pt-0">
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <button class="btn btn-success w-100 rounded-pill receiveBtn" 
                                                            data-id="${way.id}" data-name="${way.name}">
                                                        <i class="fas fa-plus me-1"></i>
                                                        {{ __('messages.receive') }}
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <button class="btn btn-primary w-100 rounded-pill sendBtn" 
                                                            data-id="${way.id}" data-name="${way.name}">
                                                        <i class="fas fa-minus me-1"></i>
                                                        {{ __('messages.send') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endcan

                                        <!-- Card Footer -->
                                        <div class="card-footer border-0 bg-transparent">
                                            <div class="d-flex justify-content-between gap-2">
                                                @can('payment_ways_show')
                                                    <a href="payment-ways/show/${way.id}" 
                                                       class="btn btn-outline-info btn-sm rounded-pill flex-fill">
                                                        <i class="fas fa-eye me-1"></i>
                                                        {{ __('messages.details') }}
                                                    </a>
                                                @endcan
                                                @can('payment_ways_update')
                                                    <button class="btn btn-outline-warning btn-sm rounded-pill flex-fill editBtn"
                                                            data-id="${way.id}"
                                                            data-name="${way.name}"
                                                            data-type="${way.type}"
                                                            data-client-type="${way.client_type}"
                                                            data-phone="${way.phone_number ?? ''}"
                                                            data-receive-limit="${way.receive_limit ?? 0}"
                                                            data-send-limit="${way.send_limit ?? 0}"
                                                            data-balance="${way.balance ?? 0}"
                                                            data-category-id="${categoryId}"
                                                            data-sub-category-id="${subCategoryId}">
                                                        <i class="fas fa-edit me-1"></i>
                                                        {{ __('messages.edit') }}
                                                    </button>
                                                @endcan
                                                @can('payment_ways_destroy')
                                                    <button class="btn btn-outline-danger btn-sm rounded-pill flex-fill deleteBtn" 
                                                            data-id="${way.id}" data-name="${way.name}">
                                                        <i class="fas fa-trash me-1"></i>
                                                        {{ __('messages.delete') }}
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#paymentWaysContainer').html(cards);
                        initSortable();
                    }
                });
            }

            function loadStats(data) {
                let totalBalance = 0;
                let totalWallets = 0;
                let totalCash = 0;
                let totalMachines = 0;

                data.forEach(way => {
                    totalBalance += parseFloat(way.balance || 0);
                    if (way.type === 'wallet') totalWallets++;
                    else if (way.type === 'cash') totalCash++;
                    else if (way.type === 'balance_machine') totalMachines++;
                });

                let statsHtml = `
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 opacity-75">{{ __('messages.total_balance') }}</h6>
                                        <div class="mb-0 fw-bold">${totalBalance.toFixed(2)}</div>
                                    </div>
                                    <div class="ms-3">
                                        <i class="fas fa-coins fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm rounded-3  ">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 opacity-75">{{ __('messages.electronic_wallets') }}</h6>
                                        <div class="mb-0 fw-bold">${totalWallets}</div>
                                    </div>
                                    <div class="ms-3">
                                        <i class="fas fa-wallet fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm rounded-3  ">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 opacity-75">{{ __('messages.cash_methods') }}</h6>
                                        <div class="mb-0 fw-bold">${totalCash}</div>
                                    </div>
                                    <div class="ms-3">
                                        <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card border-0 shadow-sm rounded-3  ">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 opacity-75">{{ __('messages.balance_machines') }}</h6>
                                        <div class="mb-0 fw-bold">${totalMachines}</div>
                                    </div>
                                    <div class="ms-3">
                                        <i class="fas fa-credit-card fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#statsCards').html(statsHtml);
            }

         
            function initSortable() {
                const container = document.getElementById('paymentWaysContainer');

                if (container.sortable) {
                    container.sortable.destroy();
                }

                Sortable.create(container, {
                    animation: 200,
                    handle: '.card',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onStart: function(evt) {
                        evt.item.style.transform = 'rotate(2deg)';
                    },
                    onEnd: function (evt) {
                        evt.item.style.transform = '';
                        const items = evt.to.querySelectorAll('[data-id]');
                        const order = Array.from(items).map(item => item.getAttribute('data-id'));
                        
                        // Show loading toast
                        showToast('{{ __('messages.saving_order') }}', 'info');
                        
                        $.ajax({
                            url: "{{ route('payment_ways.reorder') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                order: order
                            },
                            success: function(res) {
                                if (res.status) {
                                    showToast('{{ __("messages.updated_successfully") }}', 'success');
                                } else {
                                    showToast(res.message, 'error');
                                    loadPaymentWays(); 
                                }
                            },
                            error: function() {
                                showToast('{{ __("messages.something_went_wrong") }}', 'error');
                                loadPaymentWays(); 
                            }
                        });
                    }
                });

                container.sortable = Sortable.get(container);
            }

            // Create
            $('#createForm').submit(function (e) {
                e.preventDefault();
                $.post("{{ route('payment_ways.store') }}", $(this).serialize(), function (res) {
                    if (res.status) {
                        $('#createModal').modal('hide');
                        loadPaymentWays();
                        showToast('{{ __('messages.payment_way_created_successfully') }}',
                            'success');
                        $('#createForm')[0].reset();
                    } else {
                        showToast(res.message || '{{ __('messages.something_went_wrong') }}',
                            'error');
                    }
                });
            });

            // Edit
            $(document).on('click', '.editBtn', function () {
                $('#editId').val($(this).data('id'));
                $('#editName').val($(this).data('name'));
                $('#editType').val($(this).data('type'));
                $('#editCientType').val($(this).data('client-type'));
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
                        success: function (res) {
                            $('#editSubCategorySelect').html(
                                '<option value="">{{ __('messages.select_sub_category') }}</option>'
                            );
                            res.forEach(function (sub) {
                                $('#editSubCategorySelect').append(
                                    `<option value="${sub.id}" ${sub.id == subCategoryId ? 'selected' : ''}>${sub.name}</option>`
                                );
                            });
                        },
                        error: function (err) {
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

            $('#editForm').submit(function (e) {
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
                    success: function (res) {
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
                    error: function (err) {
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            });

            // Delete
            $(document).on('click', '.deleteBtn', function () {
                $('#deleteId').val($(this).data('id'));
                $('#deleteName').text($(this).data('name'));
                $('#deleteModal').modal('show');
            });

            $('#deleteForm').submit(function (e) {
                e.preventDefault();
                let id = $('#deleteId').val();
                $.ajax({
                    url: "{{ url('dashboard/payment-ways') }}/" + id,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.status) {
                            $('#deleteModal').modal('hide');
                            loadPaymentWays();
                            showToast('{{ __('messages.payment_way_deleted_successfully') }}',
                                'success');
                        } else {
                            showToast(res.message, 'error');
                        }
                    },
                    error: function (err) {
                        showToast(err.responseJSON.message, 'error');

                    }
                });
            });
        });
    </script>
@endpush