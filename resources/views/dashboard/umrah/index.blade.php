@extends('dashboard.umrah.layout')
@section('umrah_title', __('umrah.trips'))
@section('umrah')
    @can('umrah_trips_manage')
        <div class="umrah-quick-actions"><a class="btn btn-primary" href="#umrah-new-trip">{{ __('umrah.create_trip') }}</a><a
                class="btn btn-outline-primary" href="{{ route('umrah.travelers.index') }}">{{ __('umrah.travelers') }}</a>
    </div>@endcan
    <div class="row g-3 mb-3 umrah-metrics">@foreach($metrics as $label => $value)
        <div class="col-md-3">
            <div class="card h-100 mb-0">
                <div class="card-body"><span class="text-muted">{{ $label }}</span>
                    <div class="mt-2 mb-0">{{ $value }}</div>
                </div>
            </div>
    </div>@endforeach
    </div>
    <div class="card">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.trips') }}</div>
            <div class="table-responsive umrah-records-wrapper">
                <table class="table umrah-records" role="table">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th role="columnheader" scope="col">{{ __('umrah.trip') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.departure') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.return') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.capacity') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.bookings') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.status') }}</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        @forelse($trips as $trip)
                            <tr role="row">
                                <td role="cell" data-label="{{ __('umrah.trip') }}" data-field="trip">
                                    <div class="umrah-cell-value"><a
                                            href="{{ route('umrah.trips.show', $trip->id) }}">{{ $trip->name }}</a></div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.departure') }}" data-field="departure">
                                    <div class="umrah-cell-value">{{ $trip->departure_date->format('Y-m-d') }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.return') }}" data-field="return">
                                    <div class="umrah-cell-value">{{ $trip->return_date->format('Y-m-d') }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.capacity') }}" data-field="capacity">
                                    <div class="umrah-cell-value">{{ $trip->capacity }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.bookings') }}" data-field="bookings">
                                    <div class="umrah-cell-value">{{ $trip->bookings_count }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.status') }}" data-field="status">
                                    <div class="umrah-cell-value">{{ __('umrah.' . $trip->status) }}</div>
                                </td>
                            </tr>
                        @empty<tr role="row">
                            <td role="cell" colspan="6">{{ __('umrah.no_trips') }}</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>{{ $trips->links('dashboard.umrah.pagination') }}
        </div>
    </div>
    @can('umrah_trips_manage')
        <div class="card">
            <div class="card-body">
                <details id="umrah-new-trip" class="umrah-create-panel" @if($errors->any()) open @endif>
                    <summary>{{ __('umrah.create_trip') }}</summary>
                    <form method="POST" action="{{ route('umrah.trips.store') }}">@csrf
                        @include('dashboard.umrah.trip-fields', ['trip' => null, 'formKey' => 'new-trip'])
                        <button class="btn btn-primary">{{ __('umrah.create_trip_action') }}</button>
                    </form>
                </details>
            </div>
        </div>
    @endcan
@endsection