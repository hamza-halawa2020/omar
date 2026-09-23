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
                <div class="d-flex justify-content-between align-items-center mb-3 gap-3 payment-way-filters">
                    <input type="text" id="searchTransactions" class="form-control w-50"
                        placeholder="{{ __('messages.search_transactions_by_notes_or_amount...') }}">
                    <select id="filterType" class="form-control w-25">
                        <option value="">{{ __('messages.all_types') }}</option>
                        <option value="receive">{{ __('messages.receive') }}</option>
                        <option value="send">{{ __('messages.send') }}</option>
                    </select>
                    <div class="d-flex align-items-center gap-2 mb-3 payment-way-date-filter">
                        <button id="nextDay" class="btn btn-outline-primary">&rarr;</button>
                        <input type="text" id="dateRange" class="form-control w-auto" placeholder="Select date range">
                        <button id="prevDay" class="btn btn-outline-primary">&larr;</button>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="responsive-records-wrapper table-responsive">
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                            <thead class="">
                                <tr>
                                    <th class="text-center">{{ __('messages.actions') }}</th>
                                    <th class="text-center">{{ __('messages.type') }}</th>
                                    <th class="text-center">{{ __('messages.amount') }}</th>
                                    <th class="text-center">{{ __('messages.commission') }}</th>
                                    <th class="text-center">{{ __('messages.product') }}</th>
                                    <th class="text-center">{{ __('messages.client') }}</th>
                                    <th class="text-center">{{ __('messages.balance_before_transaction') }}</th>
                                    <th class="text-center">{{ __('messages.balance_after_transaction') }}</th>
                                    <th class="text-center">{{ __('messages.created_at') }}</th>
                                    <th class="text-center">{{ __('messages.last_update') }}</th>
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
            const canViewPurchasePrices = @can('purchase_prices_view') true @else false @endcan;

            let id = "{{ request()->id ?? '' }}";
            let currentPaymentWay = null; // Store current payment way data
            
            if (!id) {
                $("#errorMessage").text("{{ __('messages.no_payment_way_id_provided') }}").show().fadeOut(5000);
                return;
            }

            function initializeClientSelect2() {
                if (!$.fn.select2) {
                    return;
                }

                const $clientSelect = $('#client_id');
                if (!$clientSelect.length) {
                    return;
                }

                if ($clientSelect.hasClass('select2-hidden-accessible')) {
                    $clientSelect.select2('destroy');
                }

                $clientSelect.select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: "{{ __('messages.select_client') }}",
                    dropdownParent: $('#transactionModal'),
                    dir: $('html').attr('dir') || 'rtl',
                    ajax: {
                        url: "{{ route('clients.list') }}",
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return {
                                type: currentTransactionType(),
                                search: params.term || '',
                                limit: 100
                            };
                        },
                        processResults: function (res) {
                            return {
                                results: (res.data || []).map(function (client) {
                                    return {
                                        id: client.id,
                                        text: `${client.name} ({{ __('messages.debt') }}: ${parseFloat(client.debt || 0).toFixed(2)})`
                                    };
                                })
                            };
                        }
                    }
                });
            }

            initializeClientSelect2();

            let productOptionsHtml = '<option value="">{{ __('messages.select_product') }}</option>';
            let paymentWayOptionsHtml = '<option value="">{{ __('messages.select_payment_way') }}</option>';
            let productBatchesById = {};
            let transactionProductIndex = 0;
            let transactionPaymentIndex = 0;
            let paymentSplitModeEnabled = false;

            function initializeProductSelect2($scope = $('#transactionProductsList')) {
                if (!$.fn.select2) {
                    return;
                }

                const $productSelects = $scope.find('.product-select');
                if (!$productSelects.length) {
                    return;
                }

                function formatProduct(product) {
                    if (!product.id) return product.text;
                    const $el = $(product.element || []);
                    const metaParts = [];
                    const purchasePrice = product.purchase_price ?? $el.data('purchase-price') ?? 0;
                    const salePrice = product.sale_price ?? $el.data('sale-price') ?? 0;
                    const stock = product.stock ?? $el.data('stock') ?? 0;
                    if (canViewPurchasePrices) {
                        metaParts.push(`{{ __('messages.purchase_price') }}: ${parseFloat(purchasePrice || 0).toFixed(2)}`);
                    }
                    metaParts.push(`{{ __('messages.sale_price') }}: ${parseFloat(salePrice || 0).toFixed(2)}`);
                    metaParts.push(`{{ __('messages.stock') }}: ${stock || 0}`);

                    return $(
                        `<div class="transaction-product-option">
                            <div class="transaction-product-option__name" style="font-weight:500">${product.text}</div>
                            <small class="transaction-product-option__meta">${metaParts.join(' | ')}</small>
                        </div>`
                    );
                }

                function formatProductSelection(product) {
                    return $(`<span class="transaction-product-selection">${product.text || ''}</span>`);
                }

                $productSelects.each(function () {
                    const $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        width: '100%',
                        allowClear: true,
                        placeholder: "{{ __('messages.select_product') }}",
                        dropdownParent: $('#transactionModal'),
                        dir: $('html').attr('dir') || 'rtl',
                        templateResult: formatProduct,
                        templateSelection: formatProductSelection,
                        ajax: {
                            url: "{{ route('products.list') }}",
                            dataType: 'json',
                            delay: 300,
                            data: function (params) {
                                return {
                                    with_batches: 1,
                                    search: params.term || '',
                                    per_page: 100
                                };
                            },
                            processResults: function (res) {
                                return {
                                    results: (res.data || []).map(function (product) {
                                        let productCode = product.code ? ` [${product.code}]` : '';
                                        productBatchesById[product.id] = product.purchase_batches || [];

                                        return {
                                            id: product.id,
                                            text: `${product.name}${productCode}`,
                                            purchase_price: product.purchase_price || 0,
                                            sale_price: product.sale_price || 0,
                                            stock: product.stock || 0,
                                            purchase_batches: product.purchase_batches || []
                                        };
                                    })
                                };
                            }
                        }
                    });
                });
            }

            function buildProductRow(index) {
                return `
                    <div class="transaction-product-row" data-product-row>
                        <div class="transaction-product-select">
                            <label class="form-label small">{{ __('messages.product') }}</label>
                            <select name="products[${index}][product_id]" class="form-select product-select" data-placeholder="{{ __('messages.select_product') }}">
                                ${productOptionsHtml}
                            </select>
                        </div>
                        <div class="transaction-product-details" data-product-details></div>
                        <div class="transaction-product-quantity">
                            <label class="form-label small">{{ __('messages.quantity') }}</label>
                            <input type="number" name="products[${index}][quantity]" min="1" value="1" placeholder="{{ __('messages.quantity') }}" class="form-control product-quantity">
                        </div>
                        <div class="transaction-product-unit-price">
                            <label class="form-label small product-unit-price-label">{{ __('messages.purchase_price') }}</label>
                            <input type="number" name="products[${index}][unit_price]" min="0" step="0.01" placeholder="{{ __('messages.purchase_price') }}" class="form-control product-unit-price">
                        </div>
                        <div class="transaction-product-batch">
                            <label class="form-label small">{{ __('messages.batch') }}</label>
                            <select name="products[${index}][purchase_batch_id]" class="form-select product-batch-select" disabled>
                                <option value="">{{ __('messages.fifo') }}</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm transaction-product-remove" data-remove-product>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            }

            function resetTransactionProducts() {
                transactionProductIndex = 0;
                $('#transactionProductsList').html(buildProductRow(transactionProductIndex));
                $('#transactionProductsList [data-remove-product]').prop('disabled', true);
                initializeProductSelect2();
                syncProductSelections();
            }

            function currentTransactionType() {
                return $('#receiveForm input[name="type"]').val();
            }

            function currentUnitPriceLabel() {
                return currentTransactionType() === 'send'
                    ? '{{ __('messages.purchase_price') }}'
                    : '{{ __('messages.sale_price') }}';
            }

            function syncUnitPriceLabels() {
                const label = currentUnitPriceLabel();

                $('#transactionProductsList [data-product-row]').each(function () {
                    $(this).find('.product-unit-price-label').text(label);
                    $(this).find('.product-unit-price').attr('placeholder', label);
                });
            }

            function selectedProductIds() {
                return $('.product-select').map(function () {
                    return $(this).val();
                }).get().filter(Boolean);
            }

            function syncProductSelections() {
                const selectedIds = selectedProductIds();

                $('.product-select').each(function () {
                    const $select = $(this);
                    const currentValue = $select.val();

                    $select.find('option').each(function () {
                        const optionValue = $(this).attr('value');
                        const shouldDisable = optionValue && optionValue !== currentValue && selectedIds.includes(optionValue);
                        $(this).prop('disabled', shouldDisable);
                    });

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.trigger('change.select2');
                    }
                });
            }

            function initializeEditProductSelect2() {
                if (!$.fn.select2) {
                    return;
                }

                const $productSelect = $('#editProductId');
                if (!$productSelect.length) {
                    return;
                }

                if ($productSelect.hasClass('select2-hidden-accessible')) {
                    $productSelect.select2('destroy');
                }

                function formatEditProduct(product) {
                    if (!product.id) return product.text;
                    const $el = $(product.element);
                    const metaParts = [];
                    if (canViewPurchasePrices) {
                        metaParts.push(`{{ __('messages.purchase_price') }}: ${parseFloat($el.data('purchase-price') || 0).toFixed(2)}`);
                    }
                    metaParts.push(`{{ __('messages.sale_price') }}: ${parseFloat($el.data('sale-price') || 0).toFixed(2)}`);
                    metaParts.push(`{{ __('messages.stock') }}: ${$el.data('stock') || 0}`);

                    return $(
                        `<div>
                            <div style="font-weight:500;overflow-wrap:anywhere">${product.text}</div>
                            <small style="opacity:.7">${metaParts.join(' | ')}</small>
                        </div>`
                    );
                }

                $productSelect.select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: "{{ __('messages.select_product') }}",
                    dropdownParent: $('#editTransactionModal'),
                    dir: $('html').attr('dir') || 'rtl',
                    templateResult: formatEditProduct
                });
            }

            initializeProductSelect2();
            initializeEditProductSelect2();

            const clientsCache = {};

            function reopenSelect2($select) {
                if (!$select || !$select.length || !$select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                setTimeout(function () {
                    $select.select2('open');
                }, 0);
            }

            function renderClientOptions(clients, $activeSelect = null) {
                const selectedValue = $('#client_id').val();
                let clientOptions = '<option value="">{{ __('messages.select_client') }}</option>';
                clients.forEach(function (client) {
                    clientOptions +=
                        `<option value="${client.id}">${client.name} ({{ __('messages.debt') }}: ${parseFloat(client.debt || 0).toFixed(2)})</option>`;
                });

                $('#client_id').prop('disabled', false).html(clientOptions).val(selectedValue).trigger('change');
                reopenSelect2($activeSelect);
            }

            function setClientLoadingState() {
                $('#client_id')
                    .prop('disabled', true)
                    .html('<option value="">{{ __('messages.loading_text') }}...</option>')
                    .val('')
                    .trigger('change');
            }

            // Load clients and products functions (same as index page)
            function loadClients(type, search = '', $activeSelect = null) {
                let deferred = $.Deferred();

                if (!type) {
                    renderClientOptions([]);
                    deferred.resolve([]);
                    return deferred.promise();
                }

                const cacheKey = `${type}:${search}`;

                if (clientsCache[cacheKey]) {
                    renderClientOptions(clientsCache[cacheKey], $activeSelect);
                    deferred.resolve(clientsCache[cacheKey]);
                    return deferred.promise();
                }

                if (!search) {
                    setClientLoadingState();
                }

                $.get("{{ route('clients.list') }}", { type: type, search: search, limit: 100 })
                    .done(function (res) {
                        if (res.status) {
                            clientsCache[cacheKey] = res.data || [];
                            renderClientOptions(clientsCache[cacheKey], $activeSelect);
                            deferred.resolve(clientsCache[cacheKey]);
                        } else {
                            renderClientOptions([]);
                            showToast('{{ __('messages.something_went_wrong') }}', 'error');
                            deferred.reject();
                        }
                    })
                    .fail(function () {
                        renderClientOptions([]);
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
                        deferred.reject();
                    });

                return deferred.promise();
            }

            function loadProducts(search = '', $activeSelect = null) {
                return $.get("{{ route('products.list') }}", { with_batches: 1, search: search, per_page: 100 }, function (res) {
                    if (res.status) {
                        let productOptions = search ? productOptionsHtml : '<option value="">{{ __('messages.select_product') }}</option>';
                        res.data.forEach(function (product) {
                            let productCode = product.code ? ` [${product.code}]` : '';
                            productBatchesById[product.id] = product.purchase_batches || [];
                            const purchasePriceData = canViewPurchasePrices ? ` data-purchase-price="${product.purchase_price || 0}"` : '';
                            if (!productOptions.includes(`value="${product.id}"`)) {
                                productOptions +=
                                    `<option value="${product.id}"${purchasePriceData} data-sale-price="${product.sale_price || 0}" data-stock="${product.stock || 0}">${product.name}${productCode}</option>`;
                            }
                        });
                        productOptionsHtml = productOptions;
                        $('.product-select').each(function () {
                            const selectedValue = $(this).val();
                            $(this).html(productOptionsHtml).val(selectedValue).trigger('change');
                        });
                        initializeProductSelect2();
                        syncProductSelections();
                        reopenSelect2($activeSelect);
                    } else {
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            }

            function appendPaymentWayOptions(paymentWays) {
                paymentWays.forEach(function (way) {
                    if (!paymentWayOptionsHtml.includes(`value="${way.id}"`)) {
                        paymentWayOptionsHtml += `<option value="${way.id}">${way.name}</option>`;
                    }
                });
            }

            function refreshPaymentWaySelects($activeSelect = null) {
                $('.transaction-payment-way').each(function () {
                    const selectedValue = $(this).val();
                    $(this).html(paymentWayOptionsHtml).val(selectedValue).trigger('change');
                });

                initializePaymentWaySelect2();
                syncPaymentSelections();
                reopenSelect2($activeSelect);
            }

            function loadPaymentWayOptions(search = '', $activeSelect = null) {
                let deferred = $.Deferred();

                return $.get("{{ route('payment_ways.list') }}", { search: search, per_page: 60 }, function (res) {
                    if (res.status) {
                        if (!search) {
                            paymentWayOptionsHtml = '<option value="">{{ __('messages.select_payment_way') }}</option>';
                        }

                        appendPaymentWayOptions(res.data || []);
                        refreshPaymentWaySelects($activeSelect);
                        deferred.resolve();
                    } else {
                        deferred.reject();
                    }
                }).fail(function () {
                    deferred.reject();
                });

                return deferred.promise();
            }

            function getSelectedProductPrice($row) {
                const $selectedProduct = $row.find('.product-select').find(':selected');
                const type = $('#receiveForm input[name="type"]').val();

                if (type === 'send' && !canViewPurchasePrices) {
                    return 0;
                }

                const priceKey = type === 'send' ? 'purchase-price' : 'sale-price';

                return parseFloat($selectedProduct.data(priceKey) || 0);
            }

            function setDefaultUnitPrice($row) {
                const productPrice = getSelectedProductPrice($row);
                $row.find('.product-unit-price').val(productPrice > 0 ? productPrice.toFixed(2) : '');
            }

            function updateBatchSelect($row) {
                const productId = $row.find('.product-select').val();
                const type = currentTransactionType();
                const $batchSelect = $row.find('.product-batch-select');

                if (type !== 'receive' || !productId) {
                    $batchSelect.html('<option value="">{{ __('messages.fifo') }}</option>').val('').prop('disabled', true);
                    return;
                }

                let options = '<option value="">{{ __('messages.fifo') }}</option>';
                (productBatchesById[productId] || []).forEach(function (batch) {
                    const batchLabel = canViewPurchasePrices
                        ? `{{ __('messages.purchase_price') }}: ${parseFloat(batch.unit_cost || 0).toFixed(2)} - {{ __('messages.remaining') }}: ${batch.remaining_quantity}`
                        : `#${batch.id} - {{ __('messages.remaining') }}: ${batch.remaining_quantity}`;
                    options += `<option value="${batch.id}">${batchLabel}</option>`;
                });

                $batchSelect.html(options).val('').prop('disabled', false);
            }

            function updateProductDetails($row) {
                const $selectedProduct = $row.find('.product-select').find(':selected');
                const $details = $row.find('[data-product-details]');

                if (!$selectedProduct.val()) {
                    $details.hide().empty();
                    return;
                }

                const stockBefore = parseFloat($selectedProduct.data('stock') || 0);
                const quantity = parseFloat($row.find('.product-quantity').val()) || 1;
                const stockAfter = currentTransactionType() === 'send'
                    ? stockBefore + quantity
                    : stockBefore - quantity;
                const purchasePrice = parseFloat($selectedProduct.data('purchase-price') || 0).toFixed(2);
                const salePrice = parseFloat($selectedProduct.data('sale-price') || 0).toFixed(2);

                $details
                    .html(`
                     
                        <div class="transaction-product-detail-item">
                            <span class="transaction-product-detail-label">{{ __('messages.sale_price') }}</span>
                            <span class="transaction-product-detail-value">${salePrice}</span>
                        </div>
                        <div class="transaction-product-detail-item">
                            <span class="transaction-product-detail-label">{{ __('messages.stock_before_quantity') }}</span>
                            <span class="transaction-product-detail-value">${stockBefore}</span>
                        </div>
                        <div class="transaction-product-detail-item">
                            <span class="transaction-product-detail-label">{{ __('messages.stock_after_quantity') }}</span>
                            <span class="transaction-product-detail-value">${stockAfter}</span>
                        </div>
                    `)
                    .css('display', 'grid');
            }

            function updateTransactionAmountFromProduct() {
                let total = 0;
                $('#transactionProductsList [data-product-row]').each(function () {
                    const $row = $(this);
                    const productPrice = parseFloat($row.find('.product-unit-price').val()) || 0;
                    const qty = parseFloat($row.find('.product-quantity').val()) || 1;
                    total += productPrice * qty;
                });

                $('#amount').val(total > 0 ? total.toFixed(2) : '');
                syncSinglePaymentAmount();
            }

            function initializePaymentWaySelect2($scope = $('#transactionPaymentsList')) {
                if (!$.fn.select2) {
                    return;
                }

                $scope.find('.transaction-payment-way').each(function () {
                    const $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        width: '100%',
                        allowClear: true,
                        placeholder: "{{ __('messages.select_payment_way') }}",
                        dropdownParent: $('#transactionModal'),
                        dir: $('html').attr('dir') || 'rtl',
                        ajax: {
                            url: "{{ route('payment_ways.list') }}",
                            dataType: 'json',
                            delay: 300,
                            data: function (params) {
                                return {
                                    search: params.term || '',
                                    per_page: 60
                                };
                            },
                            processResults: function (res) {
                                return {
                                    results: (res.data || []).map(function (way) {
                                        return {
                                            id: way.id,
                                            text: way.name
                                        };
                                    })
                                };
                            }
                        }
                    });
                });
            }

            function buildPaymentRow(index) {
                return `
                    <div class="transaction-payment-row" data-payment-row>
                        <div>
                            <label class="form-label small">{{ __('messages.payment_way') }}</label>
                            <select name="payments[${index}][payment_way_id]" class="form-select transaction-payment-way">
                                ${paymentWayOptionsHtml}
                            </select>
                        </div>
                        <div class="transaction-payment-amount">
                            <label class="form-label small">{{ __('messages.amount') }}</label>
                            <input type="number" name="payments[${index}][amount]" min="0" step="0.01" class="form-control transaction-payment-amount-input">
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm transaction-payment-remove" data-remove-payment>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            }

            function transactionTotalWithCommission() {
                return (parseFloat($('#amount').val()) || 0) + (parseFloat($('#commission').val()) || 0);
            }

            function updatePaymentSplitsTotal() {
                let total = 0;
                $('#transactionPaymentsList .transaction-payment-amount-input').each(function () {
                    total += parseFloat($(this).val()) || 0;
                });

                $('[data-payment-splits-total]').text(total.toFixed(2));
            }

            function syncSinglePaymentAmount() {
                const $rows = $('#transactionPaymentsList [data-payment-row]');
                const total = transactionTotalWithCommission();

                if ($rows.length === 1) {
                    $rows.first().find('.transaction-payment-amount-input').val(total > 0 ? total.toFixed(2) : '');
                }

                updatePaymentSplitsTotal();
            }

            function syncPaymentSelections() {
                const selectedIds = $('.transaction-payment-way').map(function () {
                    return $(this).val();
                }).get().filter(Boolean);

                $('.transaction-payment-way').each(function () {
                    const $select = $(this);
                    const currentValue = $select.val();

                    $select.find('option').each(function () {
                        const optionValue = $(this).attr('value');
                        const shouldDisable = optionValue && optionValue !== currentValue && selectedIds.includes(optionValue);
                        $(this).prop('disabled', shouldDisable);
                    });
                });
            }

            function resetTransactionPayments(paymentWayId = '') {
                transactionPaymentIndex = 0;
                $('#transactionPaymentsList').html(buildPaymentRow(transactionPaymentIndex));
                $('#transactionPaymentsList [data-remove-payment]').prop('disabled', true);
                $('#transactionPaymentsList .transaction-payment-way').val(paymentWayId);
                initializePaymentWaySelect2();
                syncPaymentSelections();
                syncSinglePaymentAmount();
            }

            function setPaymentSplitMode(enabled) {
                paymentSplitModeEnabled = enabled;
                $('#transactionPaymentSplitsWrapper').toggleClass('d-none', !enabled);
                $('#addTransactionPayment').toggleClass('d-none', !enabled);
                $('#transactionPaymentSplitsWrapper').find(':input').prop('disabled', !enabled);

                if (!enabled) {
                    $('#transactionPaymentsList').empty();
                    $('[data-payment-splits-total]').text('0.00');
                }
            }

            $(document).on('select2:select change', '.product-select', function (event) {
                if (event.type === 'change' && $(this).hasClass('select2-hidden-accessible')) {
                    return;
                }

                if (event.type === 'select2:select' && event.params?.data) {
                    const product = event.params.data;
                    const $option = $(this).find(`option[value="${product.id}"]`);
                    $option
                        .data('purchase-price', product.purchase_price || 0)
                        .data('sale-price', product.sale_price || 0)
                        .data('stock', product.stock || 0);
                    productBatchesById[product.id] = product.purchase_batches || productBatchesById[product.id] || [];
                }

                syncProductSelections();
                const $row = $(this).closest('[data-product-row]');
                updateBatchSelect($row);
                setDefaultUnitPrice($row);
                updateProductDetails($row);
                updateTransactionAmountFromProduct();
            });

            $(document).on('input', '.product-quantity, .product-unit-price', function () {
                updateProductDetails($(this).closest('[data-product-row]'));
                updateTransactionAmountFromProduct();
            });

            $('#amount, #commission').on('input', syncSinglePaymentAmount);

            $('#addTransactionPayment').on('click', function () {
                transactionPaymentIndex += 1;
                const $row = $(buildPaymentRow(transactionPaymentIndex));
                $('#transactionPaymentsList').append($row);
                $('#transactionPaymentsList [data-remove-payment]').prop('disabled', $('#transactionPaymentsList [data-payment-row]').length === 1);
                initializePaymentWaySelect2($row);
                syncPaymentSelections();
                updatePaymentSplitsTotal();
            });

            $(document).on('change', '.transaction-payment-way', syncPaymentSelections);
            $(document).on('input', '.transaction-payment-amount-input', updatePaymentSplitsTotal);

            $(document).on('click', '[data-remove-payment]', function () {
                if ($('#transactionPaymentsList [data-payment-row]').length === 1) {
                    return;
                }

                $(this).closest('[data-payment-row]').remove();
                $('#transactionPaymentsList [data-remove-payment]').prop('disabled', $('#transactionPaymentsList [data-payment-row]').length === 1);
                syncPaymentSelections();
                updatePaymentSplitsTotal();
            });

            $(document).on('select2:clear', '.product-select', function () {
                const $row = $(this).closest('[data-product-row]');
                $row.find('.product-unit-price').val('');
                updateProductDetails($row);
                updateBatchSelect($row);
                syncProductSelections();
                updateTransactionAmountFromProduct();
            });

            $('#addTransactionProduct').on('click', function () {
                transactionProductIndex += 1;
                const $row = $(buildProductRow(transactionProductIndex));
                $('#transactionProductsList').append($row);
                $('#transactionProductsList [data-remove-product]').prop('disabled', $('#transactionProductsList [data-product-row]').length === 1);
                initializeProductSelect2($row);
                syncProductSelections();
                syncUnitPriceLabels();
            });

            $(document).on('click', '[data-remove-product]', function () {
                if ($('#transactionProductsList [data-product-row]').length === 1) {
                    return;
                }

                $(this).closest('[data-product-row]').remove();
                $('#transactionProductsList [data-remove-product]').prop('disabled', $('#transactionProductsList [data-product-row]').length === 1);
                syncProductSelections();
                updateTransactionAmountFromProduct();
            });

            function getSelectedEditProductPrice() {
                const $selectedProduct = $('#editProductId').find(':selected');
                const type = $('#editType').val();

                if (type === 'send' && !canViewPurchasePrices) {
                    return 0;
                }

                const priceKey = type === 'send' ? 'purchase-price' : 'sale-price';

                return parseFloat($selectedProduct.data(priceKey) || 0);
            }

            function updateEditTransactionAmountFromProduct() {
                let productPrice = getSelectedEditProductPrice();
                if (productPrice > 0) {
                    let qty = parseFloat($('#editQuantity').val()) || 1;
                    $('#editAmount').val((productPrice * qty).toFixed(2));
                }
            }

            $('#editProductId').on('select2:select change', function (event) {
                if (event.type === 'change' && $(this).hasClass('select2-hidden-accessible')) {
                    return;
                }

                updateEditTransactionAmountFromProduct();
            });

            $('#editQuantity').on('input', function () {
                updateEditTransactionAmountFromProduct();
            });

            $('#editType').on('change', updateEditTransactionAmountFromProduct);

            $('#editProductId').on('select2:clear', function () {
                $('#editAmount').val('');
            });

            // Send/Receive button handlers
            $(document).on('click', '.receiveBtn, .sendBtn', function () {
                if (!currentPaymentWay) {
                    showToast('{{ __('messages.payment_way_not_loaded') }}', 'error');
                    return;
                }

                let $btn = $(this);
                let originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin me-1"></i>{{ __('messages.loading_text') }}').prop('disabled', true);

                $('#receiveForm input[name="payment_way_id"], #receiveForm input[name="type"]').remove();
                let type = $(this).hasClass('receiveBtn') ? 'receive' : 'send';

                $('#commission').val(0);
                resetTransactionProducts();
                setPaymentSplitMode(false);

                $('#receiveForm').append(`
                    <input type="hidden" name="payment_way_id" value="${id}">
                    <input type="hidden" name="type" value="${type}">
                `);
                syncUnitPriceLabels();
                $('#transactionProductsList [data-product-row]').each(function () {
                    updateBatchSelect($(this));
                    updateProductDetails($(this));
                });

                let actionText = type === 'receive' ? '{{ __('messages.create_receive_transaction') }}' : '{{ __('messages.create_send_transaction') }}';
                $('#transactionModal .modal-title').text(`${actionText} - ${currentPaymentWay.name}`);

                let clientType = currentPaymentWay.client_type;
                
                loadProducts();
                $.when(loadPaymentWayOptions(), loadClients(clientType)).always(function () {
                    $('#transactionModal').modal('show');
                    $btn.html(originalText).prop('disabled', false);
                });
            });

            // Transaction form submission
            $('#receiveForm').submit(function (e) {
                e.preventDefault();
                let $form = $(this);

                if ($form.data('submitting')) {
                    return;
                }

                if (paymentSplitModeEnabled) {
                    let expectedTotal = transactionTotalWithCommission();
                    let paymentsTotal = 0;

                    $('#transactionPaymentsList .transaction-payment-amount-input').each(function () {
                        paymentsTotal += parseFloat($(this).val()) || 0;
                    });

                    if (Math.abs(paymentsTotal - expectedTotal) > 0.01) {
                        showToast('{{ __('messages.payment_splits_must_equal_total') }}', 'error');
                        return;
                    }
                }

                let $submitButton = $form.find('button[type="submit"]');
                let originalSubmitText = $submitButton.html();
                $form.data('submitting', true);
                $submitButton
                    .prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin me-1"></i>{{ __('messages.saving') }}');

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
                            showToast(res.message || '{{ __('messages.transaction_created_successfully') }}', 'success');
                            $('#receiveForm')[0].reset();
                            $('#commission').val(0);
                            $('#client_id').val('').trigger('change');
                            resetTransactionProducts();
                            setPaymentSplitMode(false);
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
                    },
                    complete: function () {
                        $form.data('submitting', false);
                        $submitButton.prop('disabled', false).html(originalSubmitText);
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
                $('#editCommission').val(commission || 0);
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
                    $('#editPaymentWayId').val(paymentWayId).trigger('change');
                    $('#editClientId').val(clientId);
                    $('#editProductId').val(productId).trigger('change');
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
                if ($(this).prop('disabled') || $(this).hasClass('disabled')) {
                    return;
                }

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
            function initializeEditPaymentWaySelect2() {
                if (!$.fn.select2) {
                    return;
                }

                const $select = $('#editPaymentWayId');
                if (!$select.length) {
                    return;
                }

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: "{{ __('messages.select_payment_way') }}",
                    dropdownParent: $('#editTransactionModal'),
                    dir: $('html').attr('dir') || 'rtl',
                    ajax: {
                        url: "{{ route('payment_ways.list') }}",
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return {
                                search: params.term || '',
                                per_page: 60
                            };
                        },
                        processResults: function (res) {
                            return {
                                results: (res.data || []).map(function (way) {
                                    return {
                                        id: way.id,
                                        text: way.name
                                    };
                                })
                            };
                        }
                    }
                });
            }

            function loadPaymentWaysForEdit(search = '', $activeSelect = null) {
                return $.get("{{ route('payment_ways.list') }}", { search: search, per_page: 60 }, function (res) {
                    if (res.status) {
                        const selectedValue = $('#editPaymentWayId').val();
                        let options = search ? $('#editPaymentWayId').html() : '<option value="">{{ __('messages.select_payment_way') }}</option>';
                        res.data.forEach(function (way) {
                            if (!options.includes(`value="${way.id}"`)) {
                                options += `<option value="${way.id}">${way.name}</option>`;
                            }
                        });
                        $('#editPaymentWayId').html(options).val(selectedValue).trigger('change');
                        initializeEditPaymentWaySelect2();
                        reopenSelect2($activeSelect);
                    }
                });
            }

            function loadClientsForEdit(type) {
                $.get("{{ route('clients.list') }}", { type: type, limit: 100 }, function (res) {
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
                $.get("{{ route('products.list') }}", { with_batches: 1, per_page: 100 }, function (res) {
                    if (res.status) {
                        let options = '<option value="">{{ __('messages.select_product') }}</option>';
                        res.data.forEach(function (product) {
                            let productCode = product.code ? ` [${product.code}]` : '';
                            const purchasePriceData = canViewPurchasePrices ? ` data-purchase-price="${product.purchase_price || 0}"` : '';
                            options += `<option value="${product.id}"${purchasePriceData} data-sale-price="${product.sale_price || 0}" data-stock="${product.stock || 0}">${product.name}${productCode}</option>`;
                        });
                        $('#editProductId').html(options);
                        initializeEditProductSelect2();
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

                    const totalLogs = logs.length;
                    const updateLogs = logs.filter(log => log.action === 'update').length;

                    logsHtml += `
                        <div class="mb-3 p-3 rounded-3 border">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="fw-semibold">{{ __('messages.transaction_logs') }}</div>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary">${totalLogs} {{ __('messages.logs') }}</span>
                                    <span class="badge bg-warning text-dark">${updateLogs} {{ __('messages.update') }}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    logs.forEach(log => {
                        let badgeClass = log.action === 'create' ? 'success' : log.action === 'update' ? 'warning' : 'danger';
                        const actorName = log.creator?.name || '{{ __('messages.unknown') }}';
                        const logDate = log.created_at || '{{ __('messages.unknown') }}';

                        logsHtml += `
                            <div class="shadow-sm mb-3 p-3 rounded-3 border">
                                <div class="pb-2 border-bottom mb-3">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-${badgeClass}">${actionLabels[log.action] || log.action}</span>
                                            <span class="badge bg-light text-dark border">{{ __('messages.log_id') }} #${log.id}</span>
                                        </div>
                                        <small class="text-muted">${logDate}</small>
                                    </div>
                                    <div class="small mt-2">{{ __('messages.by') }}: <span class="fw-semibold">${actorName}</span></div>
                                </div>
                                <div>
                                    ${formatLogData(log.data, log.action, log)}
                                </div>
                            </div>
                        `;
                    });
                }
                
                $('#transactionLogsContent').html(logsHtml);
            }

            function formatLogData(data, action, log = null) {
                if (!data) return '<p class="">{{ __('messages.no_data_available') }}</p>';
                
                let html = '';
                
                if (action === 'update' && data.old_data && data.new_data) {
                    const changes = getChangedFields(data.old_data, data.new_data);
                    html += `<div class="mb-2 fw-semibold">{{ __('messages.changes_made') }} (${changes.length})</div>`;
                    if (data.old_data.payment_way_id && data.new_data.payment_way_id && data.old_data.payment_way_id != data.new_data.payment_way_id) {
                        const oldPaymentWayName =
                            log?.old_payment_way_name ||
                            data.history?.old_payment_way?.name ||
                            data.old_data?.payment_way?.name ||
                            data.old_data.payment_way_id;
                        const newPaymentWayName =
                            log?.new_payment_way_name ||
                            data.history?.new_payment_way?.name ||
                            data.new_data?.payment_way?.name ||
                            data.new_data.payment_way_id;
                        html += `<div class="alert alert-warning py-2 px-3 mt-2 mb-3 small">{{ __('messages.transaction_moved_between_payment_ways') }}: <span class="fw-semibold">${oldPaymentWayName}</span> <i class="fas fa-arrow-right mx-1"></i> <span class="fw-semibold">${newPaymentWayName}</span></div>`;
                    }
                    html += formatDiffTable(changes, data, log);
                } else {
                    html += formatDataTable(data);
                }
                
                return html;
            }

            function formatDataTable(data) {
                let html = '<div class="table-responsive responsive-records-wrapper"><table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">';
                html += '<thead class="text-center"><tr class="text-center"><th class="text-center">{{ __('messages.name') }}</th><th>{{ __('messages.details') }}</th></tr></thead><tbody>';
                
                Object.entries(data).forEach(([key, value]) => {
                    const label = formatLogFieldLabel(key);
                    if (typeof value === 'object' && value !== null) {
                        if (key === 'client' || key === 'product' || key === 'payment_way') {
                            html += `<tr><td class="fw-semibold mobile-primary" data-label="{{ __('messages.name') }}">${label}</td><td data-label="{{ __('messages.details') }}">${value.name || value.id || '{{ __('messages.not_specified') }}'}</td></tr>`;
                        }
                    } else {
                        html += `<tr><td class="fw-semibold mobile-primary" data-label="{{ __('messages.name') }}">${label}</td><td data-label="{{ __('messages.details') }}">${value ?? '{{ __('messages.not_specified') }}'}</td></tr>`;
                    }
                });
                
                html += '</tbody></table></div>';
                return html;
            }

            function getChangedFields(oldData, newData) {
                const keys = new Set([...Object.keys(oldData || {}), ...Object.keys(newData || {})]);
                const changes = [];

                keys.forEach((key) => {
                    const oldVal = normalizeLogValue(oldData?.[key], key);
                    const newVal = normalizeLogValue(newData?.[key], key);
                    if (oldVal !== newVal) {
                        changes.push({
                            key,
                            label: formatLogFieldLabel(key),
                            oldVal: oldVal || '{{ __('messages.not_specified') }}',
                            newVal: newVal || '{{ __('messages.not_specified') }}',
                        });
                    }
                });

                return changes;
            }

            function normalizeLogValue(value, key = null) {
                if (value === null || value === undefined || value === '') {
                    return '';
                }

                if (typeof value === 'object') {
                    if (key === 'payment_way') {
                        return value.name || value.id || '';
                    }
                    if (key === 'client' || key === 'product') {
                        return value.name || value.id || '';
                    }
                    return JSON.stringify(value);
                }

                return String(value);
            }

            function formatDiffTable(changes, data = {}, log = null) {
                if (!changes.length) {
                    return `<div class="alert alert-info py-2 px-3 mb-0">{{ __('messages.no_changes_detected') }}</div>`;
                }

                let html = '<div class="table-responsive responsive-records-wrapper"><table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">';

                html += `
                    <thead class="text-center">
                        <tr class="text-center">
                            <th class="text-center">{{ __('messages.name') }}</th>
                            <th class="text-danger text-center">{{ __('messages.old_values') }}</th>
                            <th class="text-success text-center">{{ __('messages.new_values') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                changes.forEach((change) => {
                    let oldDisplay = change.oldVal;
                    let newDisplay = change.newVal;

                    if (change.key === 'payment_way_id') {
                        oldDisplay =
                            log?.old_payment_way_name ||
                            data?.history?.old_payment_way?.name ||
                            data?.old_data?.payment_way?.name ||
                            change.oldVal;
                        newDisplay =
                            log?.new_payment_way_name ||
                            data?.history?.new_payment_way?.name ||
                            data?.new_data?.payment_way?.name ||
                            change.newVal;
                    }

                    html += `
                        <tr>
                            <td class="fw-semibold mobile-primary" data-label="{{ __('messages.name') }}">${change.label}</td>
                            <td class="text-danger" data-label="{{ __('messages.old_values') }}">${escapeHtml(oldDisplay)}</td>
                            <td class="text-success" data-label="{{ __('messages.new_values') }}">${escapeHtml(newDisplay)}</td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                return html;
            }

            function escapeHtml(value) {
                return String(value)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function formatLogFieldLabel(key) {
                const labels = {
                    id: "{{ __('messages.id') }}",
                    type: "{{ __('messages.type') }}",
                    amount: "{{ __('messages.amount') }}",
                    commission: "{{ __('messages.commission') }}",
                    notes: "{{ __('messages.notes') }}",
                    payment_way_id: "{{ __('messages.payment_way') }}",
                    client_id: "{{ __('messages.client') }}",
                    product_id: "{{ __('messages.product') }}",
                    quantity: "{{ __('messages.quantity') }}",
                    balance_before_transaction: "{{ __('messages.balance_before_transaction') }}",
                    balance_after_transaction: "{{ __('messages.balance_after_transaction') }}",
                    created_at: "{{ __('messages.created_at') }}",
                    updated_at: "{{ __('messages.updated_at') }}",
                };

                return labels[key] || key.replace(/_/g, ' ');
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
                    const displayAmount = tx.payment_way_split_amount ?? tx.amount;
                    const displayCommission = tx.payment_way_split_commission ?? tx.commission;
                    const displayBalanceBefore = tx.payment_way_split_balance_before ?? tx.balance_before_transaction;
                    const displayBalanceAfter = tx.payment_way_split_balance_after ?? tx.balance_after_transaction;
                    const canViewLogs = Boolean(tx.is_edited);
                    const productsText = tx.products && tx.products.length
                        ? tx.products.map(item => `${item.product?.name ?? ''} x${item.quantity}`).join(', ')
                        : (tx.product?.name ?? '');
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
                            <button class="btn btn-outline-info btn-sm viewLogsBtn ${canViewLogs ? '' : 'disabled'}" 
                                    data-id="${tx.id}"
                                    title="${canViewLogs ? `{{ __('messages.view_logs') }}` : `{{ __('messages.no_logs_found') }}`}"
                                    ${canViewLogs ? '' : 'disabled aria-disabled="true"'}>
                                <i class="fas fa-history"></i>
                            </button>
                        </div>
                    `;
                    
                    txHtml += `
                        <tr>
                            <td class="mobile-actions" data-label="{{ __('messages.actions') }}">${actionsHtml}</td>
                            <td class="mobile-primary" data-label="{{ __('messages.type') }}" data-type="${tx.type}">
                                <span class="badge bg-${tx.type === 'receive' ? 'success' : 'danger'}">${translations[tx.type] ?? tx.type}</span>
                                ${tx.is_edited ? '<span class="badge bg-warning text-dark ms-1">{{ __('messages.edited') }}</span>' : ''}
                            </td>
                            <td data-label="{{ __('messages.amount') }}">${displayAmount}</td>
                            <td data-label="{{ __('messages.commission') }}">${displayCommission}</td>
                            <td data-label="{{ __('messages.product') }}">${productsText}</td>
                            <td data-label="{{ __('messages.client') }}">${tx.client?.name ?? ''}</td>
                            <td data-label="{{ __('messages.balance_before_transaction') }}">${displayBalanceBefore}</td>
                            <td data-label="{{ __('messages.balance_after_transaction') }}">${displayBalanceAfter}</td>
                            <td class="mobile-muted" data-label="{{ __('messages.created_at') }}">${tx.created_at || ''}</td>
                            <td class="mobile-muted mobile-hide" data-label="{{ __('messages.last_update') }}">${tx.updated_at || ''}</td>
                            <td class="mobile-muted mobile-hide" data-label="{{ __('messages.creator') }}">${tx.creator?.name || ''}</td>
                            <td class="mobile-muted" data-label="{{ __('messages.notes') }}">${tx.notes || ''}</td>
                            <td data-label="{{ __('messages.attachment') }}">${attachmentHtml}</td>
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
                };

                data.logs.forEach(log => {
                    let dataDetails = "";
                    if (log.data) {
                        dataDetails = `
                            <div class="mt-2">
                                <strong class="">{{ __('messages.changes') }}:</strong>
                                <table class="table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                                    <tbody>                                      
                                        ${Object.entries(log.data).map(([key, value]) => `<tr><td class="mobile-primary" data-label="{{ __('messages.name') }}">${fieldLabels[key] || key}</td><td data-label="{{ __('messages.details') }}">${value || ''}</td></tr>`).join('')}
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
