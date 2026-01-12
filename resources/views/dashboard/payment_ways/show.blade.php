@extends('dashboard.layouts.app')

@section('content')
    <div class="container-fluid px-3 py-3">
        <!-- Header -->
        <div class="mb-3">
            <div class="div fw-bold mb-1">
                <i class="fas fa-credit-card me-2 text-primary"></i>
                {{ __('messages.payment_ay_ashboard') }}
            </div>
            <p class=" mb-0">{{ __('messages.manage_transactions_and_limits') }}</p>
        </div>

        <!-- Summary Cards Grid -->
        <div class="row g-3 mb-3">
            <!-- Current Balance -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class=" small mb-2">{{ __('messages.current_balance') }}</p>
                                <div class="fw-bold mb-0" id="paymentWayBalance">0</div>
                            </div>
                            <i class="fas fa-wallet fa-2x text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class=" small mb-2">{{ __('messages.total_transactions') }}</p>
                                <div class="fw-bold mb-0" id="paymentWayTransactions">0</div>
                            </div>
                            <i class="fas fa-exchange-alt fa-2x text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receive Stats -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <p class=" small mb-3">{{ __('messages.received') }}</p>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">{{ __('messages.amount') }}</span>
                                <span class="fw-bold text-success" id="receive_amount">0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">{{ __('messages.commission') }}</span>
                                <span class="fw-bold text-warning" id="receive_commission">0</span>
                            </div>
                        </div>
                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold">{{ __('messages.total') }}</span>
                                <span class="fw-bold text-primary" id="receive_total">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Send Stats -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <p class=" small mb-3">{{ __('messages.sent') }}</p>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">{{ __('messages.amount') }}</span>
                                <span class="fw-bold text-success" id="send_amount">0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">{{ __('messages.commission') }}</span>
                                <span class="fw-bold text-warning" id="send_commission">0</span>
                            </div>
                        </div>
                        <div class="border-top pt-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold">{{ __('messages.total') }}</span>
                                <span class="fw-bold text-primary" id="send_total">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wallet Limits (shown only for wallet type) -->
        <div class="row g-3 mb-3 wallet-only" style="display: none;">
            <!-- Receive Limit -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div class="fw-bold mb-0">
                                <i class="fas fa-arrow-down me-2 text-success"></i>
                                {{ __('messages.receive_limit') }}
                            </div>
                            <span class="badge bg-primary" id="paymentWayReceiveLimit">0</span>
                        </div>
                        <div class="progress rounded-pill mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" id="receiveProgressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="row text-center g-2">
                            <div class="col-6">
                                <small class="">{{ __('messages.used') }}</small>
                                <p class="fw-bold mb-0" id="paymentWayReceiveUsed">0</p>
                            </div>
                            <div class="col-6">
                                <small class="">{{ __('messages.remaining') }}</small>
                                <p class="fw-bold mb-0 text-success" id="paymentWayReceiveRemaining">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Send Limit -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div class="fw-bold mb-0">
                                <i class="fas fa-arrow-up me-2 text-primary"></i>
                                {{ __('messages.send_limit') }}
                            </div>
                            <span class="badge bg-primary" id="paymentWaySendLimit">0</span>
                        </div>
                        <div class="progress rounded-pill mb-3" style="height: 8px;">
                            <div class="progress-bar bg-primary" id="sendProgressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="row text-center g-2">
                            <div class="col-6">
                                <small class="">{{ __('messages.used') }}</small>
                                <p class="fw-bold mb-0" id="paymentWaySendUsed">0</p>
                            </div>
                            <div class="col-6">
                                <small class="">{{ __('messages.remaining') }}</small>
                                <p class="fw-bold mb-0 text-success" id="paymentWaySendRemaining">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Grand Net Summary -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 bg-gradient">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class=" small mb-1 fs-5">{{ __('messages.grand_net') }}</p>
                                <div class="fw-bold mb-0" id="grandNet">0</div>
                            </div>
                            <i class="fas fa-chart-line fa-3x text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @can('transactions_store')
            <div class="d-flex justify-content-center gap-2 gap-md-3 mb-3 flex-wrap">
                <button class="btn btn-success rounded-pill px-3 px-md-5 receiveBtn shadow-sm" id="receiveBtn">
                    <i class="fas fa-arrow-down me-2"></i>
                    <span class="d-none d-sm-inline">{{ __('messages.receive') }}</span>
                    <span class="d-sm-none">{{ __('messages.receive') }}</span>
                </button>
                <button class="btn btn-primary rounded-pill px-3 px-md-5 sendBtn shadow-sm" id="sendBtn">
                    <i class="fas fa-arrow-up me-2"></i>
                    <span class="d-none d-sm-inline">{{ __('messages.send') }}</span>
                    <span class="d-sm-none">{{ __('messages.send') }}</span>
                </button>
            </div>
        @endcan

        <!-- Tabs -->
        <ul class="nav nav-tabs border-bottom mb-3" id="paymentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('messages.details') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                    <i class="fas fa-list me-2"></i>
                    {{ __('messages.transactions') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">
                    <i class="fas fa-history me-2"></i>
                    {{ __('messages.changes') }}
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Details Tab -->
            <div class="tab-pane fade" id="details" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="fw-bold mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            {{ __('messages.payment_way_information') }}
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class=" small d-block mb-1">{{ __('messages.name') }}</label>
                                    <p class="fw-semibold mb-0" id="paymentWayName">-</p>
                                </div>
                                <div class="mb-3">
                                    <label class=" small d-block mb-1">{{ __('messages.phone_number') }}</label>
                                    <p class="fw-semibold mb-0" id="paymentWayPhone">-</p>
                                </div>
                                <div class="mb-3">
                                    <label class=" small d-block mb-1">{{ __('messages.category') }}</label>
                                    <p class="fw-semibold mb-0" id="paymentWayCategory">-</p>
                                </div>
                                <div class="mb-3">
                                    <label class=" small d-block mb-1">{{ __('messages.sub_category') }}</label>
                                    <p class="fw-semibold mb-0" id="paymentWaySubCategory">-</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class=" small d-block mb-1">{{ __('messages.type') }}</label>
                                    <p class="fw-semibold mb-0" id="paymentWayType">-</p>
                                </div>
                                <div class="mb-3">
                                    <label class=" small d-block mb-1">{{ __('messages.created_by') }}</label>
                                    <p class="fw-semibold mb-0" id="paymentWayCreator">-</p>
                                </div>
                                <div class="mb-3">
                                    <label class=" small d-block mb-1">{{ __('messages.created_at') }}</label>
                                    <p class="fw-semibold mb-0" id="paymentWayCreatedAt">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Tab -->
            <div class="tab-pane fade show active" id="transactions">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                    <input type="text" id="searchTransactions" class="form-control w-50"
                        placeholder="{{ __('messages.search_transactions_by_notes_or_amount...') }}">
                    <select id="filterType" class="form-control w-25">
                        <option value="">{{ __('messages.all_types') }}</option>
                        <option value="receive">{{ __('messages.receive') }}</option>
                        <option value="send">{{ __('messages.send') }}</option>
                    </select>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <button id="nextDay" class="btn btn-outline-primary">&rarr;</button>
                        <input type="text" id="dateRange" class="form-control w-auto" placeholder="Select date range">
                        <button id="prevDay" class="btn btn-outline-primary">&larr;</button>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="" style="overflow: auto">
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                            <thead class="">
                                <tr>
                                    <th class="text-center">{{ __('messages.actions') }}</th>
                                    <th class="text-center">{{ __('messages.type') }}</th>
                                    <th class="text-center">{{ __('messages.amount') }}</th>
                                    <th class="text-center">{{ __('messages.commission') }}</th>
                                    <th class="text-center">{{ __('messages.client') }}</th>
                                    <th class="text-center">{{ __('messages.balance_before_transaction') }}</th>
                                    <th class="text-center">{{ __('messages.balance_after_transaction') }}</th>
                                    <th class="text-center">{{ __('messages.created_at') }}</th>
                                    <th class="text-center">{{ __('messages.creator') }}</th>
                                    <th class="text-center">{{ __('messages.notes') }}</th>
                                    <th class="text-center">{{ __('messages.attachment') }}</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsTableBody"></tbody>
                        </table>
                </div>
            </div>

            <!-- Logs Tab -->
            <div class="tab-pane fade" id="logs" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="fw-bold mb-3">
                            <i class="fas fa-history me-2 text-primary"></i>
                            {{ __('messages.activity_logs') }}
                        </div>
                        <div id="logsTimeline"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">{{ __('messages.loading...') }}</span>
            </div>
            <p class="">{{ __('messages.loading...') }}</p>
        </div>

        <!-- Alerts -->
        <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;"></div>
    </div>

    <!-- Transaction Modal -->
    @include('dashboard.payment_ways.transactionModal')
    
    <!-- Edit Transaction Modal -->
    @include('dashboard.payment_ways.editTransactionModal')
    
    <!-- Transaction Logs Modal -->
    @include('dashboard.payment_ways.transactionLogsModal')
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let id = "{{ request()->id ?? '' }}";
            let currentPaymentWay = null; // Store current payment way data
            
            if (!id) {
                $("#errorMessage").text("{{ __('messages.no_payment_way_id_provided') }}").show().fadeOut(5000);
                return;
            }

            // Load clients and products functions (same as index page)
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

            // Send/Receive button handlers
            $(document).on('click', '.receiveBtn, .sendBtn', function () {
                if (!currentPaymentWay) {
                    showToast('{{ __('messages.payment_way_not_loaded') }}', 'error');
                    return;
                }

                $('#receiveForm input[name="payment_way_id"], #receiveForm input[name="type"]').remove();
                let type = $(this).hasClass('receiveBtn') ? 'receive' : 'send';

                $('#receiveForm').append(`
                    <input type="hidden" name="payment_way_id" value="${id}">
                    <input type="hidden" name="type" value="${type}">
                `);

                let actionText = type === 'receive' ? '{{ __('messages.create_receive_transaction') }}' : '{{ __('messages.create_send_transaction') }}';
                $('#transactionModal .modal-title').text(`${actionText} - ${currentPaymentWay.name}`);

                let clientType = currentPaymentWay.client_type;
                
                loadProducts();
                loadClients(clientType);
                $('#transactionModal').modal('show');
            });

            // Transaction form submission
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
                            // Refresh the payment way data
                            let currentDateRange = $("#dateRange").val();
                            if (currentDateRange) {
                                let dates = currentDateRange.split(' to ');
                                if (dates.length === 2) {
                                    fetchPaymentWay(dates[0], dates[1]);
                                } else {
                                    fetchPaymentWay(dates[0], dates[0]);
                                }
                            } else {
                                fetchDay(currentDate);
                            }
                        } else {
                            showToast(res.message || '{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    },
                    error: function (err) {
                        showToast(`{{ __('messages.something_went_wrong') }}: ${err.responseJSON?.message || err.responseText}`, 'error');
                    }
                });
            });

            // Edit transaction handler
            $(document).on('click', '.editTransactionBtn', function () {
                let transactionId = $(this).data('id');
                let type = $(this).data('type');
                let amount = $(this).data('amount');
                let commission = $(this).data('commission');
                let notes = $(this).data('notes');
                let clientId = $(this).data('client-id');
                let productId = $(this).data('product-id');
                let quantity = $(this).data('quantity');
                let paymentWayId = $(this).data('payment-way-id');
                let attachment = $(this).data('attachment');

                // Populate form
                $('#editTransactionId').val(transactionId);
                $('#editType').val(type);
                $('#editAmount').val(amount);
                $('#editCommission').val(commission);
                $('#editNotes').val(notes);
                $('#editQuantity').val(quantity);

                // Load payment ways
                loadPaymentWaysForEdit();
                
                // Load clients and products
                if (currentPaymentWay) {
                    loadClientsForEdit(currentPaymentWay.client_type);
                }
                loadProductsForEdit();

                // Set selected values after loading
                setTimeout(() => {
                    $('#editPaymentWayId').val(paymentWayId);
                    $('#editClientId').val(clientId);
                    $('#editProductId').val(productId);
                }, 500);

                // Show current attachment
                if (attachment) {
                    $('#currentAttachment').html(`
                        <small class="">{{ __('messages.current_attachment') }}: </small>
                        <a href="${attachment}" target="_blank" class="text-primary">${attachment.split('/').pop()}</a>
                    `);
                } else {
                    $('#currentAttachment').html('');
                }

                $('#editTransactionModal').modal('show');
            });

            // Edit transaction form submission
            $('#editTransactionForm').submit(function (e) {
                e.preventDefault();
                let transactionId = $('#editTransactionId').val();
                let formData = new FormData(this);

                if (!formData.get('payment_way_id')) {
                    formData.delete('payment_way_id');
                }

                $.ajax({
                    url: `/dashboard/transactions/${transactionId}`,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res.status) {
                            $('#editTransactionModal').modal('hide');
                            showToast('{{ __('messages.transaction_updated_successfully') }}', 'success');
                            $('#editTransactionForm')[0].reset();
                            
                            // Check if payment way changed
                            let newPaymentWayId = $('#editPaymentWayId').val();
                            let currentPaymentWayId = id; // Current page payment way ID
                            
                            if (newPaymentWayId && newPaymentWayId != currentPaymentWayId) {
                                // Payment way changed, redirect to new payment way page
                                showToast('{{ __('messages.transaction_moved_to_new_payment_way') }}', 'info');
                                setTimeout(() => {
                                    window.location.href = `/dashboard/payment-ways/show/${newPaymentWayId}`;
                                }, 2000);
                            } else {
                                // Same payment way, just refresh the data
                                let currentDateRange = $("#dateRange").val();
                                if (currentDateRange) {
                                    let dates = currentDateRange.split(' to ');
                                    if (dates.length === 2) {
                                        fetchPaymentWay(dates[0], dates[1]);
                                    } else {
                                        fetchPaymentWay(dates[0], dates[0]);
                                    }
                                } else {
                                    fetchDay(currentDate);
                                }
                            }
                        } else {
                            showToast(res.message || '{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    },
                    error: function (err) {
                        showToast(`{{ __('messages.something_went_wrong') }}: ${err.responseJSON?.message || err.responseText}`, 'error');
                    }
                });
            });

            // View logs handler
            $(document).on('click', '.viewLogsBtn', function () {
                let transactionId = $(this).data('id');
                
                $.ajax({
                    url: `/dashboard/transactions/${transactionId}`,
                    type: "GET",
                    success: function (res) {
                        if (res.status) {
                            displayTransactionLogs(res.data.logs);
                            $('#transactionLogsModal').modal('show');
                        } else {
                            showToast(res.message || '{{ __('messages.something_went_wrong') }}', 'error');
                        }
                    },
                    error: function (err) {
                        showToast(`{{ __('messages.something_went_wrong') }}: ${err.responseJSON?.message || err.responseText}`, 'error');
                    }
                });
            });

            // Helper functions for edit modal
            function loadPaymentWaysForEdit() {
                $.get("{{ route('payment_ways.list') }}", function (res) {
                    if (res.status) {
                        let options = '<option value="">{{ __('messages.select_payment_way') }}</option>';
                        res.data.forEach(function (way) {
                            options += `<option value="${way.id}">${way.name}</option>`;
                        });
                        $('#editPaymentWayId').html(options);
                    }
                });
            }

            function loadClientsForEdit(type) {
                $.get("{{ route('clients.list') }}", { type: type }, function (res) {
                    if (res.status) {
                        let options = '<option value="">{{ __('messages.select_client') }}</option>';
                        res.data.forEach(function (client) {
                            options += `<option value="${client.id}">${client.name} ({{ __('messages.debt') }}: ${parseFloat(client.debt || 0).toFixed(2)})</option>`;
                        });
                        $('#editClientId').html(options);
                    }
                });
            }

            function loadProductsForEdit() {
                $.get("{{ route('products.list') }}", function (res) {
                    if (res.status) {
                        let options = '<option value="">{{ __('messages.select_product') }}</option>';
                        res.data.forEach(function (product) {
                            options += `<option value="${product.id}">${product.name} ({{ __('messages.sale_price') }}: ${parseFloat(product.sale_price || 0).toFixed(2)}) ({{ __('messages.stock') }}: ${product.stock || 0})</option>`;
                        });
                        $('#editProductId').html(options);
                    }
                });
            }

            function displayTransactionLogs(logs) {
                let logsHtml = '';
                
                if (!logs || logs.length === 0) {
                    logsHtml = '<p class=" text-center">{{ __('messages.no_logs_found') }}</p>';
                } else {
                    let actionLabels = {
                        create: "{{ __('messages.create') }}",
                        update: "{{ __('messages.update') }}",
                        delete: "{{ __('messages.delete') }}"
                    };

                    logs.forEach(log => {
                        let badgeClass = log.action === 'create' ? 'success' : log.action === 'update' ? 'warning' : 'danger';
                        
                        logsHtml += `
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-${badgeClass} me-2">${actionLabels[log.action] || log.action}</span>
                                        <small class="">{{ __('messages.by') }}: ${log.creator?.name || 'Unknown'}</small>
                                    </div>
                                    <small class="">${log.created_at}</small>
                                </div>
                                <div class="card-body">
                                    ${formatLogData(log.data, log.action)}
                                </div>
                            </div>
                        `;
                    });
                }
                
                $('#transactionLogsContent').html(logsHtml);
            }

            function formatLogData(data, action) {
                if (!data) return '<p class="">{{ __('messages.no_data_available') }}</p>';
                
                let html = '';
                
                if (action === 'update' && data.old_data && data.new_data) {
                    html += '<div>{{ __('messages.changes_made') }}:</div>';
                    html += '<div class="row">';
                    html += '<div class="col-md-6"><div class="text-danger">{{ __('messages.old_values') }}</div>';
                    html += formatDataTable(data.old_data);
                    html += '</div>';
                    html += '<div class="col-md-6"><div class="text-success">{{ __('messages.new_values') }}</div>';
                    html += formatDataTable(data.new_data);
                    html += '</div>';
                    html += '</div>';
                } else {
                    html += formatDataTable(data);
                }
                
                return html;
            }

            function formatDataTable(data) {
                let html = '<table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">';
                
                Object.entries(data).forEach(([key, value]) => {
                    if (typeof value === 'object' && value !== null) {
                        if (key === 'client' || key === 'product' || key === 'payment_way') {
                            html += `<tr><td><strong>${key.replace('_', ' ').toUpperCase()}</strong></td><td>${value.name || value.id || 'N/A'}</td></tr>`;
                        }
                    } else {
                        html += `<tr><td><strong>${key.replace('_', ' ').toUpperCase()}</strong></td><td>${value || 'N/A'}</td></tr>`;
                    }
                });
                
                html += '</table>';
                return html;
            }

            let currentDate = new Date();
            let dateRangePicker = $("#dateRange").flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [new Date()],
                onReady: function () {
                    let today = formatDate(new Date());
                    fetchPaymentWay(today, today);
                },
                onChange: function (selectedDates) {
                    if (selectedDates.length === 2) {
                        currentDate = selectedDates[0];
                        let start = formatDate(selectedDates[0]);
                        let end = formatDate(selectedDates[1]);
                        fetchPaymentWay(start, end);
                    } else if (selectedDates.length === 1) {
                        currentDate = selectedDates[0];
                        let day = formatDate(selectedDates[0]);
                        fetchPaymentWay(day, day);
                    }
                }
            });

            $("#prevDay").on("click", function () {
                currentDate.setDate(currentDate.getDate() - 1);
                fetchDay(currentDate);
            });

            $("#nextDay").on("click", function () {
                currentDate.setDate(currentDate.getDate() + 1);
                fetchDay(currentDate);
            });

            function fetchDay(date) {
                let formatted = formatDate(date);
                dateRangePicker.setDate([formatted, formatted], true);
                fetchPaymentWay(formatted, formatted);
            }

            function filterTransactions() {
                let searchText = $("#searchTransactions").val().toLowerCase();
                let filterType = $("#filterType").val();

                $("#transactionsTableBody tr").each(function () {
                    let row = $(this);
                    let type = row.find("td:first").data("type");
                    let rowText = row.text().toLowerCase();

                    let matchesSearch = rowText.includes(searchText);
                    let matchesType = !filterType || type === filterType;

                    row.toggle(matchesSearch && matchesType);
                });
            }


            $("#searchTransactions").on("keyup", filterTransactions);
            $("#filterType").on("change", filterTransactions);

            function renderPaymentWay(res) {
                let data = res.data;
                let statistics = res.statistics || {};
                
                // Store current payment way data for send/receive functionality
                currentPaymentWay = data;

                if (data.type === 'wallet') {
                    $('.wallet-only').show();
                } else {
                    $('.wallet-only').hide();
                }

                $("#paymentWayName").text(data.name || '');
                $("#paymentWayType").text(data.type ? data.type.charAt(0).toUpperCase() + data.type.slice(1) : '');
                $("#paymentWayPhone").text(data.phone_number || '');
                $("#paymentWayCategory").text(data.category?.name || '');
                $("#paymentWaySubCategory").text(data.subCategory?.name || '');
                $("#paymentWayBalance").text(data.balance || 0);
                $("#paymentWayCreator").text(data.creator?.name || '');
                $("#paymentWayCreatedAt").text(data.created_at || '');
                $("#paymentWayTransactions").text(data.transactions?.length || 0);
                $("#paymentWayReceiveLimit").text(statistics.limits?.receive_limit || 0);
                $("#paymentWayReceiveUsed").text(statistics.limits?.receive_used || 0);
                $("#paymentWayReceiveRemaining").text(statistics.limits?.receive_remaining || 0);
                $("#paymentWaySendLimit").text(statistics.limits?.send_limit || 0);
                $("#paymentWaySendUsed").text(statistics.limits?.send_used || 0);
                $("#paymentWaySendRemaining").text(statistics.limits?.send_remaining || 0);
                $("#receive_amount").text(statistics.receive?.receive_amount || 0);
                $("#receive_commission").text(statistics.receive?.receive_commission || 0);
                $("#receive_total").text(statistics.receive?.receive_total || 0);
                $("#send_amount").text(statistics.send?.send_amount || 0);
                $("#send_commission").text(statistics.send?.send_commission || 0);
                $("#send_total").text(statistics.send?.send_total || 0);
                $("#grandNet").text(statistics.grand_net || 0);

                const translations = {
                    receive: "{{ __('messages.receive') }}",
                    send: "{{ __('messages.send') }}",
                };

                let txHtml = "";
                data.transactions.forEach(tx => {
                    let attachmentHtml = tx.attachment ?`<a href="${tx.attachment}" target="_blank" class="text-primary">View</a>` : '';
                    if (tx.attachment && /\.(jpg|jpeg|png|gif)$/i.test(tx.attachment)) {
                        attachmentHtml =`<a href="${tx.attachment}" target="_blank"><img src="${tx.attachment}" alt="Attachment" class="img-thumbnail" style="max-width: 50px; max-height: 50px;"></a>`;
                    }
                    
                    let actionsHtml = `
                        <div class="d-flex gap-1 justify-content-center">
                            @can('transactions_update')
                                <button class="btn btn-outline-warning btn-sm editTransactionBtn" 
                                        data-id="${tx.id}"
                                        data-type="${tx.type}"
                                        data-amount="${tx.amount}"
                                        data-commission="${tx.commission || 0}"
                                        data-notes="${tx.notes || ''}"
                                        data-client-id="${tx.client?.id || ''}"
                                        data-product-id="${tx.product?.id || ''}"
                                        data-quantity="${tx.quantity || ''}"
                                        data-payment-way-id="${tx.payment_way?.id || ''}"
                                        data-attachment="${tx.attachment || ''}"
                                        title="{{ __('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endcan
                            <button class="btn btn-outline-info btn-sm viewLogsBtn" 
                                    data-id="${tx.id}"
                                    title="{{ __('messages.view_logs') }}">
                                <i class="fas fa-history"></i>
                            </button>
                        </div>
                    `;
                    
                    txHtml += `
                        <tr>
                            <td>${actionsHtml}</td>
                            <td data-type="${tx.type}"><span class="badge bg-${tx.type === 'receive' ? 'success' : 'danger'}">${translations[tx.type] ?? tx.type}</span></td>
                            <td>${tx.amount}</td>
                            <td>${tx.commission}</td>
                            <td>${tx.client?.name ?? ''}</td>
                            <td>${tx.balance_before_transaction}</td>
                            <td>${tx.balance_after_transaction}</td>
                            <td>${tx.created_at || ''}</td>
                            <td>${tx.creator?.name || ''}</td>
                            <td>${tx.notes || ''}</td>
                            <td>${attachmentHtml}</td>
                        </tr>
                    `;
                });
                $("#transactionsTableBody").html(txHtml);

                let logsHtml = "";
                let actionLabels = {
                    create: "{{ __('messages.create') }}",
                    update: "{{ __('messages.update') }}",
                    delete: "{{ __('messages.delete') }}"
                };
                let fieldLabels = {
                    id: "{{ __('messages.id') }}",
                    name: "{{ __('messages.name') }}",
                    type: "{{ __('messages.type') }}",
                    balance: "{{ __('messages.balance') }}",
                    created_at: "{{ __('messages.created_at') }}",
                    created_by: "{{ __('messages.created_by') }}",
                    send_limit: "{{ __('messages.send_limit') }}",
                    updated_at: "{{ __('messages.updated_at') }}",
                    category_id: "{{ __('messages.category_id') }}",
                    phone_number: "{{ __('messages.phone_number') }}",
                    receive_limit: "{{ __('messages.receive_limit') }}",
                    sub_category_id: "{{ __('messages.sub_category_id') }}",
                    creator: "{{ __('messages.creator') }}",
                    category: "{{ __('messages.category') }}",
                    sub_category: "{{ __('messages.sub_category') }}",
                };

                data.logs.forEach(log => {
                    let dataDetails = "";
                    if (log.data) {
                        dataDetails = `
                            <div class="mt-2">
                                <strong class="">{{ __('messages.changes') }}:</strong>
                                <table class="table table-bordered table-sm table bordered-table sm-table mb-0">
                                    <tbody>                                      
                                        ${Object.entries(log.data).map(([key, value]) => `<tr><td class="">${fieldLabels[key] || key}</td><td>${value || ''}</td></tr>`).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                    logsHtml += `
                        <li class="border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-${log.action === 'create' ? 'success' : log.action === 'update' ? 'warning' : 'danger'} me-2">
                                        ${actionLabels[log.action] || log.action}
                                    </span>
                                    <div class="small ">{{ __('messages.created_at') }}: ${log.created_at || ''}</div>
                                </div>
                                <span class="badge bg-secondary">{{ __('messages.logs') }} #${log.id}</span>
                            </div>
                            ${dataDetails}
                        </li>
                    `;
                });
                $("#logsTimeline").html(logsHtml);
            }

            function fetchPaymentWay(startDate = null, endDate = null) {
                $("#loader").show();
                let data = {};

                if (startDate && endDate) {
                    data.time = "custom";
                    data.start_date = startDate;
                    data.end_date = endDate;
                } else {
                    data.time = "today";
                }

                $.ajax({
                    url: `/dashboard/payment-ways/show-list/${id}`,
                    type: "GET",
                    data: data,
                    success: function (res) {
                        $("#loader").hide();
                        if (!res.status) {
                            $("#errorMessage").text(res.message || "Error fetching payment way details").show().fadeOut(5000);
                            return;
                        }
                        renderPaymentWay(res);
                    },
                    error: function (xhr) {
                        $("#loader").hide();
                        $("#errorMessage").text(xhr.responseJSON?.message ||"Error fetching payment way details").show().fadeOut(5000);
                    }
                });
            }

            function formatDate(date) {
                let year = date.getFullYear();
                let month = String(date.getMonth() + 1).padStart(2, "0");
                let day = String(date.getDate()).padStart(2, "0");
                return `${year}-${month}-${day}`;
            }

            fetchDay(currentDate);
        });
    </script>
@endpush