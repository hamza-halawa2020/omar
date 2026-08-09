<div class="modal fade" id="logActionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="logActionForm">
            @csrf
            <input type="hidden" id="logIphoneId">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">
                        {{ __('messages.add_iphone_log') }} - <span id="logIphoneName"></span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.action_type') }}</label>
                        <select name="action_type" id="logActionType" class="form-control" required>
                            <option value="sale">{{ __('messages.iphone_action_sale') }}</option>
                            <option value="return">{{ __('messages.iphone_action_return') }}</option>
                            <option value="maintenance">{{ __('messages.iphone_action_maintenance') }}</option>
                            <option value="expense">{{ __('messages.iphone_action_expense') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.payment_way') }}</label>
                        <select name="payment_way_id" class="form-control" required>
                            <option value="">{{ __('messages.select_payment_way') }}</option>
                            @foreach ($paymentWays as $paymentWay)
                                <option value="{{ $paymentWay->id }}">{{ $paymentWay->name }} - {{ number_format($paymentWay->balance, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.client') }} <span id="logClientLabelHint">({{ __('messages.optional') }})</span></label>
                        <input type="hidden" id="lockedLogClientId">
                        <select name="client_id" id="logClientId" class="form-control">
                            <option value="">{{ __('messages.none') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                        <span class="mobile-helper-text" id="logClientHelperText"></span>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.amount') }}</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-primary btn-sm radius-8">{{ __('messages.save') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm radius-8" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
