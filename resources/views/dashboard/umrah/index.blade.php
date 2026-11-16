@extends('dashboard.umrah.layout')
@section('umrah')
    <div class="row g-3 mb-3">@foreach($metrics as $label => $value)
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
            <div>{{ __('umrah.trips') }}</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('umrah.trip') }}</th>
                            <th>{{ __('umrah.departure') }}</th>
                            <th>{{ __('umrah.return') }}</th>
                            <th>{{ __('umrah.capacity') }}</th>
                            <th>{{ __('umrah.bookings') }}</th>
                            <th>{{ __('umrah.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trips as $trip)
                            <tr>
                                <td><a href="{{ route('umrah.trips.show', $trip->id) }}">{{ $trip->name }}</a></td>
                                <td>{{ $trip->departure_date->format('Y-m-d') }}</td>
                                <td>{{ $trip->return_date->format('Y-m-d') }}</td>
                                <td>{{ $trip->capacity }}</td>
                                <td>{{ $trip->bookings_count }}</td>
                                <td>{{ __('umrah.' . $trip->status) }}</td>
                            </tr>
                        @empty<tr>
                            <td colspan="6">{{ __('umrah.no_trips') }}</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>{{ $trips->links() }}
        </div>
    </div>
    @can('umrah_trips_manage')
        <div class="card">
            <div class="card-body">
                <div>{{ __('umrah.create_trip') }}</div>
                <form method="POST" action="{{ route('umrah.trips.store') }}">@csrf
                    @include('dashboard.umrah.trip-fields', ['trip' => null, 'formKey' => 'new-trip'])
                    <button class="btn btn-primary">{{ __('umrah.create_trip_action') }}</button>
                </form>
            </div>
        </div>
    @endcan
@endsection