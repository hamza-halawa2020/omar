@extends('dashboard.umrah.layout')
@section('umrah')
    <div class="card">
        <div class="card-body">
            <div>{{ $trip->name }}</div>
            <p>{{ __('umrah.departure') }} {{ $trip->departure_date->format('Y-m-d') }} — {{ __('umrah.return') }}
                {{ $trip->return_date->format('Y-m-d') }} — {{ __('umrah.capacity') }} {{ $trip->capacity }} —
                {{ __('umrah.' . $trip->status) }}
            </p>
            @can('umrah_trips_manage')
                <details>
                    <summary>{{ __('umrah.edit_trip') }}</summary>
                    <form method="POST" action="{{ route('umrah.trips.update', $trip->id) }}">@csrf @method('PUT')
                        @include('dashboard.umrah.trip-fields', ['formKey' => 'trip'])<button
                            class="btn btn-primary">{{ __('umrah.save_trip') }}</button>
                    </form>
            </details>@endcan
        </div>
    </div>
    @foreach($trip->programs as $program)
        <div class="card">
            <div class="card-body">
                <div>{{ $program->name }}</div>
                <p>{{ __('umrah.makkah') }}: {{ $program->makkah_hotel }} ({{ $program->makkah_nights }}
                    {{ __('umrah.nights') }}) — {{ __('umrah.madinah') }}: {{ $program->madinah_hotel }}
                    ({{ $program->madinah_nights }} {{ __('umrah.nights') }})
                </p>
                <p>{{ $program->services }}</p>
                @foreach($program->prices as $price)
                    <div class="border rounded p-3 mb-2">{{ $price->label }} — {{ $price->amount }} {{ $price->currency }} —
                        {{ $price->is_active ? __('umrah.available') : __('umrah.unavailable') }}
                        @can('umrah_trips_manage')
                            <details>
                                <summary>{{ __('umrah.edit_price') }}</summary>
                                <form method="POST" action="{{ route('umrah.prices.update', [$program->id, $price->id]) }}">@csrf
                                    @method('PUT')
                                    @include('dashboard.umrah.price-fields', ['formKey' => 'price-' . $price->id])<button
                                        class="btn btn-primary">{{ __('umrah.save_price') }}</button>
                                </form>
                        </details>@endcan
                    </div>
                @endforeach
                @can('umrah_trips_manage')
                    <details>
                        <summary>{{ __('umrah.add_price_tier') }}</summary>
                        <form method="POST" action="{{ route('umrah.prices.store', $program->id) }}">@csrf
                            @include('dashboard.umrah.price-fields', ['price' => null, 'formKey' => 'new-price-' . $program->id])<button
                                class="btn btn-primary">{{ __('umrah.add_price') }}</button>
                        </form>
                    </details>
                    <details>
                        <summary>{{ __('umrah.edit_program') }}</summary>
                        <form method="POST" action="{{ route('umrah.programs.update', [$trip->id, $program->id]) }}">@csrf
                            @method('PUT')
                            @include('dashboard.umrah.program-fields', ['formKey' => 'program-' . $program->id])<button
                                class="btn btn-primary">{{ __('umrah.save_program') }}</button>
                        </form>
                    </details>
                @endcan
            </div>
        </div>
    @endforeach
    @can('umrah_trips_manage')
        <div class="card">
            <div class="card-body">
                <details>
                    <summary>{{ __('umrah.add_trip_program') }}</summary>
                    <form method="POST" action="{{ route('umrah.programs.store', $trip->id) }}">@csrf
                        @include('dashboard.umrah.program-fields', ['program' => null, 'formKey' => 'new-program'])<button
                            class="btn btn-primary">{{ __('umrah.add_program') }}</button>
                    </form>
                </details>
            </div>
        </div>
    @endcan
    @can('umrah_bookings_manage')
        <div class="card">
            <div class="card-body">
                <div>{{ __('umrah.book_traveler') }}</div>
                <p>{{ __('umrah.booking_needs') }} <a
                        href="{{ route('umrah.travelers.index') }}">{{ __('umrah.register_traveler_action') }}</a>
                    {{ __('umrah.booking_prerequisites') }}
                </p>
                <form method="POST" action="{{ route('umrah.bookings.store') }}">@csrf<input type="hidden" name="trip_id"
                        value="{{ $trip->id }}">
                    <p class="text-muted">{{ __('umrah.agreed_amount_help') }}</p>
                    @include('dashboard.umrah.fields', [
                        'formKey' => 'booking',
                        'fields' => [
                            ['traveler_id', __('umrah.traveler'), 'select', old('traveler_id', ''), true, $travelers->mapWithKeys(fn($t) => [$t->id => $t->name . ' — ' . $t->phone])->all()],
                            ['program_price_id', __('umrah.program_tier'), 'select', old('program_price_id', ''), true, $trip->programs->flatMap(fn($p) => $p->prices->where('is_active', true)->map(fn($v) => ['id' => $v->id, 'label' => $p->name . ' / ' . $v->label . ' / ' . $v->amount . ' ' . $v->currency]))->pluck('label', 'id')->all()],
                            ['agreed_amount', __('umrah.agreed_amount'), 'number', old('agreed_amount', '')],
                            ['price_adjustment_reason', __('umrah.price_adjustment_reason'), 'text', old('price_adjustment_reason', '')],
                            ['notes', __('umrah.booking_notes'), 'textarea', old('notes', '')],
                        ]
                    ])<button class="btn btn-primary" @disabled($trip->status !== 'open')>{{ __('umrah.confirm_booking_agreement') }}</button>
                </form>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-body">
            <div>{{ __('umrah.manifest') }}</div><button class="btn btn-outline-secondary no-print mb-3"
                onclick="window.print()">{{ __('umrah.print_manifest') }}</button>
            @include('dashboard.umrah.booking-table'){{ $bookings->links() }}
        </div>
    </div>
@endsection