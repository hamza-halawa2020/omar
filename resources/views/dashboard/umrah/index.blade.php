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
                <div class="card-body"><span class="">{{ $label }}</span>
                    <div class="mt-2 mb-0">{{ $value }}</div>
                </div>
            </div>
    </div>@endforeach
    </div>
    <div class="card">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.trips') }}</div>
            <div class="table-responsive responsive-records-wrapper">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('umrah.trip') }}</th>
                            <th class="text-center">{{ __('umrah.departure') }}</th>
                            <th class="text-center">{{ __('umrah.return') }}</th>
                            <th class="text-center">{{ __('umrah.capacity') }}</th>
                            <th class="text-center">{{ __('umrah.bookings') }}</th>
                            <th class="text-center">{{ __('umrah.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trips as $trip)
                            <tr>
                                <td class="mobile-primary" data-label="{{ __('umrah.trip') }}"><a href="{{ route('umrah.trips.show', $trip->id) }}">{{ $trip->name }}</a></td>
                                <td data-label="{{ __('umrah.departure') }}">{{ $trip->departure_date->format('Y-m-d') }}</td>
                                <td data-label="{{ __('umrah.return') }}">{{ $trip->return_date->format('Y-m-d') }}</td>
                                <td data-label="{{ __('umrah.capacity') }}">{{ $trip->capacity }}</td>
                                <td data-label="{{ __('umrah.bookings') }}">{{ $trip->bookings_count }}</td>
                                <td data-label="{{ __('umrah.status') }}">{{ __('umrah.' . $trip->status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('umrah.no_trips') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-table-pagination id="tripsPagination" :paginator="$trips" />
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