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
                        <select name="client_id" id="client_id" class="form-select">
                            <option value="">{{ __('messages.select_client') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product_id" class="form-label">{{ __('messages.product') }}</label>
                        <div class="d-flex gap-3 justify-content-stretch">
                            <select name="product_id" id="product_id" class="form-select">
                                <option value="">{{ __('messages.select_product') }}</option>
                            </select>

                            <input type="number" name="quantity" id="quantity" placeholder="{{ __('messages.quantity') }}" class="form-control">
                        </div>

                    </div>


                    <div class="mb-3">
                        <label for="amount" class="form-label">{{ __('messages.amount') }}</label>
                        <input type="number" name="amount" id="amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="commission" class="form-label">{{ __('messages.commission') }}</label>
                        <input required type="number" name="commission" id="commission" class="form-control">
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
