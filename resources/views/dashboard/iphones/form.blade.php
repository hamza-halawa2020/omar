@php
    $prefix = $prefix ?? 'create';
    $idPrefix = $prefix === 'edit' ? 'edit' : 'create';
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label>{{ __('messages.device_type') }}</label>
        <input type="text" name="device_type" id="{{ $idPrefix }}DeviceType" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label>{{ __('messages.currency') }}</label>
        <input type="text" name="currency" id="{{ $idPrefix }}Currency" class="form-control" value="SAR" required>
    </div>
    <div class="col-12 mb-3">
        <label>{{ __('messages.device_details') }}</label>
        <textarea name="device_details" id="{{ $idPrefix }}DeviceDetails" class="form-control" rows="3"></textarea>
    </div>
    <div class="col-md-6 mb-3">
        <label>{{ __('messages.purchase_price_sar') }}</label>
        <input type="number" name="purchase_price_sar" id="{{ $idPrefix }}PurchasePriceSar" class="form-control" step="0.01">
    </div>
    <div class="col-md-6 mb-3">
        <label>{{ __('messages.purchase_price_egp') }}</label>
        <input type="number" name="purchase_price_egp" id="{{ $idPrefix }}PurchasePriceEgp" class="form-control iphone-calculation" data-prefix="{{ $idPrefix }}" step="0.01" required>
    </div>
    <div class="col-md-6 mb-3">
        <label>{{ __('messages.extra_expenses') }}</label>
        <input type="number" name="extra_expenses" id="{{ $idPrefix }}ExtraExpenses" class="form-control iphone-calculation" data-prefix="{{ $idPrefix }}" step="0.01" value="0">
    </div>
    <div class="col-md-6 mb-3">
        <label>{{ __('messages.total_purchase_with_expenses') }}</label>
        <input type="number" id="{{ $idPrefix }}TotalPurchase" class="form-control" step="0.01" readonly>
    </div>
    <div class="col-md-6 mb-3">
        <label>{{ __('messages.sale_price_egp') }}</label>
        <input type="number" name="sale_price_egp" id="{{ $idPrefix }}SalePriceEgp" class="form-control iphone-calculation" data-prefix="{{ $idPrefix }}" step="0.01">
    </div>
    <div class="col-md-6 mb-3">
        <label>{{ __('messages.net_profit_after_sale') }}</label>
        <input type="number" id="{{ $idPrefix }}NetProfit" class="form-control" step="0.01" readonly>
    </div>
</div>
