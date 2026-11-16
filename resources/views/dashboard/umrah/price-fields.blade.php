@include('dashboard.umrah.fields', [
    'fields' => [
        ['label', __('umrah.price_label'), 'text', $price?->label, true],
        ['amount', __('umrah.price_per_person'), 'number', $price?->amount, true],
        ['currency', __('umrah.currency'), 'select', $price?->currency ?? 'EGP', true, ['EGP' => __('umrah.currency_egp'), 'SAR' => __('umrah.currency_sar'), 'USD' => __('umrah.currency_usd')]],
        ['is_active', __('umrah.price_availability'), 'select', (int) ($price?->is_active ?? true), true, [1 => __('umrah.available'), 0 => __('umrah.unavailable')]],
    ]
])