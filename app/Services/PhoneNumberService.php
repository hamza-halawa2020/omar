<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberService
{
    public function countryCodeOptions(): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $options = [];

        foreach ($phoneUtil->getSupportedRegions() as $region) {
            if ($region === 'IL') {
                continue;
            }
            $code = '+'.$phoneUtil->getCountryCodeForRegion($region);
            $options[] = ['region' => $region, 'code' => $code, 'label' => "{$code} ({$region})"];
        }

        usort($options, fn (array $a, array $b) => [$a['code'], $a['region']] <=> [$b['code'], $b['region']]);

        return $options;
    }

    public function split(?string $phone): array
    {
        $default = ['country_code' => '+20', 'phone_number' => $phone ?? ''];
        if (! $phone) {
            return $default;
        }
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($phone, 'EG');
            if ($phoneUtil->isValidNumber($number)) {
                return ['country_code' => '+'.$number->getCountryCode(), 'phone_number' => $phoneUtil->getNationalSignificantNumber($number)];
            }
        } catch (NumberParseException) {
            // Keep legacy entries visible for correction rather than silently changing them.
        }

        return $default;
    }

    public function international(string $countryCode, string $nationalNumber): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $input = trim($nationalNumber);
        if (str_starts_with($input, '00')) {
            $input = '+'.substr($input, 2);
        }
        if (! str_starts_with($input, '+')) {
            $input = $countryCode.$input;
        }
        try {
            $number = $phoneUtil->parse($input, null);
            if ($phoneUtil->isValidNumber($number) && '+'.$number->getCountryCode() === $countryCode) {
                return $phoneUtil->format($number, PhoneNumberFormat::E164);
            }
        } catch (NumberParseException) {
            // Report the error on the form field instead of allowing an unusable WhatsApp number.
        }

        throw ValidationException::withMessages(['phone_number' => __('umrah.invalid_phone_number')]);
    }
}
