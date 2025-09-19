<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="receiveForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.create_transaction') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-3">
                        <div>
                            <label>{{ __('messages.amount') }}</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>
                        <div>
                            <label>{{ __('messages.commission') }}</label>
                            <input type="number" name="commission" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.attachment') }}</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.notes') }}</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">{{ __('messages.save') }}</button>
                    <button type="button" class="btn btn-secondary btn-sm"
                        data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
