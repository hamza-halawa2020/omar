@include('dashboard.umrah.fields', [
    'fields' => [
        ['name', __('umrah.program_name'), 'text', $program?->name, true],
        ['makkah_hotel', __('umrah.makkah_hotel'), 'text', $program?->makkah_hotel],
        ['madinah_hotel', __('umrah.madinah_hotel'), 'text', $program?->madinah_hotel],
        ['makkah_nights', __('umrah.makkah_nights'), 'number', $program?->makkah_nights ?? 0, true],
        ['madinah_nights', __('umrah.madinah_nights'), 'number', $program?->madinah_nights ?? 0, true],
        ['services', __('umrah.services'), 'textarea', $program?->services],
    ]
])