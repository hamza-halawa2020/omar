<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="editTransactionModalLabel">{{ __('messages.edit_transaction') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTransactionForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="transaction_id" id="editTransactionId">

                    <div class="mb-3">
                        <label for="editClientId" class="form-label">{{ __('messages.client') }}</label>
                        <select name="client_id" id="editClientId" class="form-select">
                            <option value="">{{ __('messages.select_client') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editProductId" class="form-label">{{ __('messages.product') }}</label>
                        <div class="d-flex gap-3 justify-content-stretch">
                            <select name="product_id" id="editProductId" class="form-select">
                                <option value="">{{ __('messages.select_product') }}</option>
                            </select>
                            <input type="number" name="quantity" id="editQuantity" placeholder="{{ __('messages.quantity') }}" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editPaymentWayId" class="form-label">{{ __('messages.payment_way') }}</label>
                        <select name="payment_way_id" id="editPaymentWayId" class="form-select">
                            <option value="">{{ __('messages.select_payment_way') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editType" class="form-label">{{ __('messages.type') }}</label>
                        <select name="type" id="editType" class="form-select" required>
                            <option value="receive">{{ __('messages.receive') }}</option>
                            <option value="send">{{ __('messages.send') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editAmount" class="form-label">{{ __('messages.amount') }}</label>
                        <input type="number" name="amount" id="editAmount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="editCommission" class="form-label">{{ __('messages.commission') }}</label>
                        <input type="number" required name="commission" id="editCommission" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="editNotes" class="form-label">{{ __('messages.notes') }}</label>
                        <textarea name="notes" id="editNotes" class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editAttachment" class="form-label">{{ __('messages.attachment') }}</label>
                        <input type="file" name="attachment" id="editAttachment" class="form-control">
                        <div id="currentAttachment" class="mt-2"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning btn-sm">{{ __('messages.update') }}</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>