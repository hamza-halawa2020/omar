@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container-fluid px-3">
        <!-- Header Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm   rounded-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mobile-stack-header">
                            <div>
                                <div class="mb-1 fw-bold">
                                    <i class="fas fa-credit-card me-2"></i>
                                    {{ __('messages.payment_ways') }}
                                </div>
                                <p class="mb-0 opacity-75">{{ __('messages.payment_ways_management') }}</p>
                            </div>
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                @can('transactions_store')
                                    <button class="btn btn-success btn-lg rounded-pill px-3 shadow-sm splitReceiveBtn">
                                        <i class="fas fa-plus me-2"></i>
                                        {{ __('messages.receive') }} - {{ __('messages.payment_splits') }}
                                    </button>
                                    <button class="btn btn-primary btn-lg rounded-pill px-3 shadow-sm splitSendBtn">
                                        <i class="fas fa-minus me-2"></i>
                                        {{ __('messages.send') }} - {{ __('messages.payment_splits') }}
                                    </button>
                                @endcan
                                @can('payment_ways_store')
                                    <button class="btn bg-success btn-lg rounded-pill px-3 shadow-sm"
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
        </div>

        <!-- Stats Cards -->
        <div class="row mb-3" id="statsCards">
            <!-- Stats will be loaded via AJAX -->
        </div>

        <!-- Payment Ways Grid -->
        <div id="paymentWaysContainer" class="row g-3 mobile-card-grid">
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
            const canReorderPaymentWays = @can('payment_ways_reorder') true @else false @endcan;
            const canViewPurchasePrices = @can('purchase_prices_view') true @else false @endcan;

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
                    dir: $('html').attr('dir') || 'rtl'
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
                    const $el = $(product.element);
                    const metaParts = [];
                    if (canViewPurchasePrices) {
                        metaParts.push(`{{ __('messages.purchase_price') }}: ${parseFloat($el.data('purchase-price') || 0).toFixed(2)}`);
                    }
                    metaParts.push(`{{ __('messages.sale_price') }}: ${parseFloat($el.data('sale-price') || 0).toFixed(2)}`);
                    metaParts.push(`{{ __('messages.stock') }}: ${$el.data('stock') || 0}`);

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
                        templateSelection: formatProductSelection
                    });
                });
            }

            initializeProductSelect2();

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

            const clientsCache = {};

            function renderClientOptions(clients) {
                let clientOptions = '<option value="">{{ __('messages.select_client') }}</option>';
                clients.forEach(function (client) {
                    clientOptions +=
                        `<option value="${client.id}">${client.name} ({{ __('messages.debt') }}: ${parseFloat(client.debt || 0).toFixed(2)})</option>`;
                });

                $('#client_id').prop('disabled', false).html(clientOptions).val('').trigger('change');
            }

            function setClientLoadingState() {
                $('#client_id')
                    .prop('disabled', true)
                    .html('<option value="">{{ __('messages.loading_text') }}...</option>')
                    .val('')
                    .trigger('change');
            }

            function loadClients(type) {
                let deferred = $.Deferred();
                let cacheKey = type || 'all';

                if (clientsCache[cacheKey]) {
                    renderClientOptions(clientsCache[cacheKey]);
                    deferred.resolve(clientsCache[cacheKey]);
                    return deferred.promise();
                }

                setClientLoadingState();

                $.get("{{ route('clients.list') }}", { type: type })
                    .done(function (res) {
                        if (res.status) {
                            clientsCache[cacheKey] = res.data || [];
                            renderClientOptions(clientsCache[cacheKey]);
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
            function loadProducts() {
                $.get("{{ route('products.list') }}", function (res) {
                    if (res.status) {
                        let productOptions = '<option value="">{{ __('messages.select_product') }}</option>';
                        productBatchesById = {};
                        res.data.forEach(function (product) {
                            let productCode = product.code ? ` [${product.code}]` : '';
                            productBatchesById[product.id] = product.purchase_batches || [];
                            const purchasePriceData = canViewPurchasePrices ? ` data-purchase-price="${product.purchase_price || 0}"` : '';
                            productOptions +=
                                `<option value="${product.id}"${purchasePriceData} data-sale-price="${product.sale_price || 0}" data-stock="${product.stock || 0}">${product.name}${productCode}</option>`;
                        });
                        productOptionsHtml = productOptions;
                        $('.product-select').each(function () {
                            $(this).html(productOptionsHtml).val('').trigger('change');
                        });
                        initializeProductSelect2();
                        syncProductSelections();
                    } else {
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
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

            $(document).on('click', '.receiveBtn, .sendBtn', function () {
                // Add button loading state
                let $btn = $(this);
                let originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin me-1"></i>{{ __('messages.loading_text') }}').prop('disabled', true);

                $('#receiveForm input[name="payment_way_id"], #receiveForm input[name="type"]').remove();
                let type = $(this).hasClass('receiveBtn') ? 'receive' : 'send';
                let paymentWayId = $(this).data('id');

                $('#commission').val(0);
                resetTransactionProducts();
                setPaymentSplitMode(false);

                $('#receiveForm').append(`
                        <input type="hidden" name="payment_way_id" value="${paymentWayId}">
                        <input type="hidden" name="type" value="${type}">
                    `);
                syncUnitPriceLabels();
                $('#transactionProductsList [data-product-row]').each(function () {
                    updateBatchSelect($(this));
                    updateProductDetails($(this));
                });

                $('#transactionModal .modal-title').text(type === 'receive' ?
                    '{{ __('messages.create_receive_transaction') }}' :
                    '{{ __('messages.create_send_transaction') }}');


                let clientType = $(this).closest('.card').find('span[data-client-type]').attr('data-client-type');
                
                loadProducts();

                loadClients(clientType).always(function () {
                    $('#transactionModal').modal('show');
                    $btn.html(originalText).prop('disabled', false);
                });
                        
            });

            $(document).on('click', '.splitReceiveBtn, .splitSendBtn', function () {
                let $btn = $(this);
                let originalText = $btn.html();
                let type = $btn.hasClass('splitReceiveBtn') ? 'receive' : 'send';

                $btn.html('<i class="fas fa-spinner fa-spin me-1"></i>{{ __('messages.loading_text') }}').prop('disabled', true);
                $('#receiveForm input[name="payment_way_id"], #receiveForm input[name="type"]').remove();
                $('#commission').val(0);
                resetTransactionProducts();
                setPaymentSplitMode(true);
                resetTransactionPayments();

                $('#receiveForm').append(`<input type="hidden" name="type" value="${type}">`);
                syncUnitPriceLabels();

                $('#transactionModal .modal-title').text(type === 'receive'
                    ? '{{ __('messages.create_receive_transaction') }} - {{ __('messages.payment_splits') }}'
                    : '{{ __('messages.create_send_transaction') }} - {{ __('messages.payment_splits') }}');

                loadProducts();
                loadClients(null).always(function () {
                    $('#transactionModal').modal('show');
                    $btn.html(originalText).prop('disabled', false);
                });
            });


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
                            loadPaymentWays();
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
                        paymentWayOptionsHtml = '<option value="">{{ __('messages.select_payment_way') }}</option>';
                        res.data.forEach((way) => {
                            paymentWayOptionsHtml += `<option value="${way.id}">${way.name}</option>`;
                        });

                        res.data.forEach((way, i) => {
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
                                                            data-balance="${way.balance ?? 0}">
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
                let walletBalance = 0;
                let totalCash = 0;
                let cashBalance = 0;
                let totalMachines = 0;
                let machineBalance = 0;

                data.forEach(way => {
                    let balance = parseFloat(way.balance || 0);
                    totalBalance += balance;
                    if (way.type === 'wallet') {
                        totalWallets++;
                        walletBalance += balance;
                    } else if (way.type === 'cash') {
                        totalCash++;
                        cashBalance += balance;
                    } else if (way.type === 'balance_machine') {
                        totalMachines++;
                        machineBalance += balance;
                    }
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
                                        <div class="mb-0 fw-bold">${totalWallets} <span class="text-success ms-1">(${walletBalance.toFixed(2)})</span></div>
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
                                        <div class="mb-0 fw-bold">${totalCash} <span class="text-success ms-1">(${cashBalance.toFixed(2)})</span></div>
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
                                        <div class="mb-0 fw-bold">${totalMachines} <span class="text-success ms-1">(${machineBalance.toFixed(2)})</span></div>
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

            const mobileSortableQuery = window.matchMedia('(max-width: 767.98px), (pointer: coarse)');

            function isMobileSortableDisabled() {
                return mobileSortableQuery.matches;
            }

            function destroySortable(container) {
                if (container && container.sortable) {
                    container.sortable.destroy();
                    container.sortable = null;
                }
            }

            function initSortable() {
                const container = document.getElementById('paymentWaysContainer');

                destroySortable(container);

                if (!canReorderPaymentWays || !container || isMobileSortableDisabled() || typeof Sortable === 'undefined') {
                    return;
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

            function handleSortableViewportChange() {
                initSortable();
            }

            if (typeof mobileSortableQuery.addEventListener === 'function') {
                mobileSortableQuery.addEventListener('change', handleSortableViewportChange);
            } else if (typeof mobileSortableQuery.addListener === 'function') {
                mobileSortableQuery.addListener(handleSortableViewportChange);
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
