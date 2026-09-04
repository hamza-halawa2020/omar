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
            overflow-wrap: anywhere;
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
            grid-template-columns: minmax(0, 1fr) minmax(7.5rem, 10rem);
            gap: 1rem;
            align-items: start;
        }

        .transaction-product-row .transaction-product-select {
            min-width: 0;
        }

        @media (max-width: 575.98px) {
            .transaction-product-row {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
        }
    </style>
@endonce

<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
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
                        <label for="product_id" class="form-label">{{ __('messages.product') }}</label>
                        <div class="transaction-product-row">
                            <div class="transaction-product-select">
                                <select name="product_id" id="product_id" class="form-select" data-placeholder="{{ __('messages.select_product') }}">
                                    <option value="">{{ __('messages.select_product') }}</option>
                                </select>
                            </div>

                            <input type="number" name="quantity" id="quantity" placeholder="{{ __('messages.quantity') }}" class="form-control">
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
