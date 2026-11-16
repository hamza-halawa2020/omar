@include('dashboard.umrah.fields', [
    'fields' => [
        ['name', __('umrah.trip_name'), 'text', old('name', $trip?->name), true],
        ['capacity', __('umrah.seats'), 'number', old('capacity', $trip?->capacity ?? 50), true],
        ['departure_date', __('umrah.departure_date'), 'date', old('departure_date', $trip?->departure_date), true],
        ['return_date', __('umrah.return_date'), 'date', old('return_date', $trip?->return_date), true],
        ['transport', __('umrah.transport'), 'text', old('transport', $trip?->transport)],
        ['status', __('umrah.status'), 'select', old('status', $trip?->status ?? 'draft'), true, collect(config('umrah.trip_statuses'))->mapWithKeys(fn($s) => [$s => __('umrah.' . $s)])->all()],
        ['notes', __('umrah.internal_notes'), 'textarea', old('notes', $trip?->notes)],
    ]
])