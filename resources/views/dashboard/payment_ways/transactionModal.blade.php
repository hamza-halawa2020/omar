@once
    <style>
        #transactionModal,
        #editTransactionModal {
            --tm-select-bg: var(--white);
            --tm-select-border: var(--input-form-light);
            --tm-select-text: var(--text-primary-light);
            --tm-select-placeholder: var(--text-secondary-light);
            --tm-select-focus: var(--primary-600);
            --tm-select-focus-shadow: rgba(72, 127, 255, 0.2);
            --tm-select-dropdown-shadow: 0 0.5rem 1rem rgba(15, 23, 42, 0.12);
        }

        [data-theme=dark] #transactionModal,
        [data-theme=dark] #editTransactionModal {
            --tm-select-focus-shadow: rgba(72, 127, 255, 0.28);
            --tm-select-dropdown-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.35);
        }

        #transactionModal .select2-container,
        #editTransactionModal .select2-container {
            width: 100% !important;
            min-width: 0;
        }

        #transactionModal .select2-container .select2-selection--single,
        #editTransactionModal .select2-container .select2-selection--single {
            height: 38px;
            background-color: var(--tm-select-bg);
            border: 1px solid var(--tm-select-border);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            padding: 0 0.75rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        #transactionModal .select2-container--default .select2-selection--single .select2-selection__rendered,
        #editTransactionModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding: 0;
            color: var(--tm-select-text);
            display: block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #transactionModal .select2-container--default .select2-selection--single .select2-selection__placeholder,
        #editTransactionModal .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: var(--tm-select-placeholder);
        }

        #transactionModal .select2-container--default .select2-selection--single .select2-selection__arrow,
        #editTransactionModal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            left: 0.5rem;
            right: auto;
        }

        #transactionModal .select2-container--default .select2-selection--single .select2-selection__arrow b,
        #editTransactionModal .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: var(--tm-select-placeholder) transparent transparent transparent;
        }

        #transactionModal .select2-container--open .select2-selection--single,
        #transactionModal .select2-container--default.select2-container--focus .select2-selection--single,
        #editTransactionModal .select2-container--open .select2-selection--single,
        #editTransactionModal .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--tm-select-focus);
            box-shadow: 0 0 0 0.2rem var(--tm-select-focus-shadow);
        }

        #transactionModal .select2-dropdown,
        #editTransactionModal .select2-dropdown {
            background-color: var(--tm-select-bg);
            border: 1px solid var(--tm-select-border);
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: var(--tm-select-dropdown-shadow);
        }

        #transactionModal .select2-search--dropdown,
        #editTransactionModal .select2-search--dropdown {
            padding: 0.5rem;
            background-color: var(--tm-select-bg);
        }

        #transactionModal .select2-search--dropdown .select2-search__field,
        #editTransactionModal .select2-search--dropdown .select2-search__field {
            background-color: var(--tm-select-bg);
            border: 1px solid var(--tm-select-border);
            border-radius: 0.4rem;
            color: var(--tm-select-text);
            padding: 0.375rem 0.6rem;
        }

        #transactionModal .select2-search--dropdown .select2-search__field::placeholder,
        #editTransactionModal .select2-search--dropdown .select2-search__field::placeholder {
            color: var(--tm-select-placeholder);
        }

        #transactionModal .select2-results,
        #editTransactionModal .select2-results {
            background-color: var(--tm-select-bg);
        }

        #transactionModal .select2-results__option,
        #editTransactionModal .select2-results__option {
            color: var(--tm-select-text);
            padding: 0.5rem 0.75rem;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .transaction-product-option,
        .transaction-product-selection {
            display: block;
            max-width: 100%;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .transaction-product-option__name,
        .transaction-product-option__meta {
            display: block;
            max-width: 100%;
            min-width: 0;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .transaction-product-option__meta {
            opacity: 0.7;
            line-height: 1.4;
        }

        #transactionModal .select2-container--default .select2-results__option--selected,
        #editTransactionModal .select2-container--default .select2-results__option--selected {
            background-color: var(--primary-50);
            color: var(--tm-select-text);
        }

        #transactionModal .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable,
        #editTransactionModal .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: var(--tm-select-focus);
            color: #fff;
        }

        .transaction-product-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr) 2.375rem;
            gap: 0.75rem;
            align-items: end;
            padding: 0.75rem;
            border: 1px solid var(--neutral-200);
            border-radius: 0.5rem;
            background-color: var(--neutral-50);
        }

        .transaction-product-row .transaction-product-select {
            grid-column: 1 / -1;
            min-width: 0;
            overflow: hidden;
        }

        .transaction-product-quantity {
            min-width: 0;
        }

        .transaction-product-unit-price {
            min-width: 0;
        }

        .transaction-product-batch {
            min-width: 0;
        }

        .transaction-product-remove {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 575.98px) {
            .transaction-product-row {
                grid-template-columns: minmax(0, 1fr) 2.375rem;
                gap: 0.75rem;
            }

            .transaction-product-select {
                grid-column: 1 / -1;
            }
        }
    </style>
@endonce

<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="transactionModalLabel">{{ __('messages.create_transaction') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="receiveForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_way_id">
                    <input type="hidden" name="type">

                    <div class="mb-3">
                        <label for="client_id" class="form-label">{{ __('messages.client') }}</label>
                        <select name="client_id" id="client_id" class="form-select" data-placeholder="{{ __('messages.select_client') }}">
                            <option value="">{{ __('messages.select_client') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                            <label class="form-label mb-0">{{ __('messages.products') }}</label>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addTransactionProduct">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('messages.add_product') }}
                            </button>
                        </div>
                        <div id="transactionProductsList" class="d-flex flex-column gap-2">
                            <div class="transaction-product-row" data-product-row>
                                <div class="transaction-product-select">
                                    <label class="form-label small">{{ __('messages.product') }}</label>
                                    <select name="products[0][product_id]" class="form-select product-select" data-placeholder="{{ __('messages.select_product') }}">
                                        <option value="">{{ __('messages.select_product') }}</option>
                                    </select>
                                </div>
                                <div class="transaction-product-quantity">
                                    <label class="form-label small">{{ __('messages.quantity') }}</label>
                                    <input type="number" name="products[0][quantity]" min="1" value="1" placeholder="{{ __('messages.quantity') }}" class="form-control product-quantity">
                                </div>
                                <div class="transaction-product-unit-price">
                                    <label class="form-label small">{{ __('messages.purchase_price') }}</label>
                                    <input type="number" name="products[0][unit_price]" min="0" step="0.01" placeholder="{{ __('messages.purchase_price') }}" class="form-control product-unit-price">
                                </div>
                                <div class="transaction-product-batch">
                                    <label class="form-label small">{{ __('messages.batch') }}</label>
                                    <select name="products[0][purchase_batch_id]" class="form-select product-batch-select" disabled>
                                        <option value="">{{ __('messages.fifo') }}</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm transaction-product-remove" data-remove-product disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">{{ __('messages.amount') }}</label>
                        <input type="number" name="amount" id="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="commission" class="form-label">{{ __('messages.commission') }}</label>
                        <input required type="number" name="commission" id="commission" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachment" class="form-label">{{ __('messages.attachment') }}</label>
                        <input type="file" name="attachment" id="attachment" class="form-control">
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success btn-sm">{{ __('messages.save') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
