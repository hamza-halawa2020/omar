@include('dashboard.umrah.fields', [
    'fields' => [
        ['name', __('umrah.arabic_name'), 'text', old('name', $traveler?->name), true],
        ['english_name', __('umrah.english_name'), 'text', old('english_name', $traveler?->english_name)],
    ]
])
@php
    $phoneParts = app(\App\Services\PhoneNumberService::class)->split(old('phone', $traveler?->phone));
    $selectedCountryCode = old('country_code', $phoneParts['country_code']);
@endphp
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label" for="{{ $formKey }}-country-code">{{ __('messages.country_code') }} *</label>
        <select name="country_code" id="{{ $formKey }}-country-code"
            class="form-select country-code-select @error('country_code') is-invalid @enderror"
            autocomplete="tel-country-code" dir="ltr" required>
            @foreach($countryCodeOptions as $countryCode)
                <option value="{{ $countryCode['code'] }}" @selected($countryCode['code'] === $selectedCountryCode)>
                    {{ $countryCode['label'] }}</option>
            @endforeach
        </select>
        @error('country_code')
        <div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="{{ $formKey }}-phone-number">{{ __('messages.phone_number') }} *</label>
        <input type="tel" name="phone_number" id="{{ $formKey }}-phone-number"
            value="{{ old('phone_number', $phoneParts['phone_number']) }}"
            class="form-control @error('phone_number') is-invalid @enderror" dir="ltr" inputmode="tel"
            autocomplete="tel-national" maxlength="40" required>
        <small class="text-muted">{{ __('umrah.national_phone_help') }}</small>
        @error('phone_number')
        <div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
@include('dashboard.umrah.fields', [
    'fields' => [
        ['nationality', __('umrah.nationality'), 'text', old('nationality', $traveler?->nationality)],
        ['birth_date', __('umrah.birth_date'), 'date', old('birth_date', $traveler?->birth_date)],
        ['passport_number', __('umrah.passport_number'), 'text', old('passport_number', $traveler?->passport_number)],
        ['passport_expires_at', __('umrah.passport_expiry'), 'date', old('passport_expires_at', $traveler?->passport_expires_at)],
        ['whatsapp_enabled', __('umrah.whatsapp_customer'), 'select', (int) old('whatsapp_enabled', $traveler?->whatsapp_enabled ?? true), true, [1 => __('umrah.enabled'), 0 => __('umrah.disabled_customer')]],
        ['notes', __('umrah.private_notes'), 'textarea', old('notes', $traveler?->notes)],
    ]
])