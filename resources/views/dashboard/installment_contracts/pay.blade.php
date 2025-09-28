<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="payForm">
            @csrf
            <input type="hidden" name="installment_id" id="payInstallmentId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.pay_installment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.amount') }}</label>
                        <input type="number" step="0.01" name="amount" id="payAmount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.payment_date') }}</label>
                        <input type="date" name="payment_date" id="payDate" class="form-control" required value="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-success btn-sm radius-8">{{ __('messages.pay') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm radius-8" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
