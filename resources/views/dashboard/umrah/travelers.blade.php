@extends('dashboard.umrah.layout')
@section('umrah')
    <div class="card">
        <div class="card-body">
            <div>{{ __('umrah.travelers') }}</div>
            <form method="GET" class="d-flex gap-2 mb-3"><input class="form-control" name="q" value="{{ $search }}"
                    placeholder="{{ __('umrah.search_hint') }}" aria-label="{{ __('umrah.search_hint') }}"><button
                    class="btn btn-outline-primary">{{ __('umrah.search') }}</button></form>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('umrah.name') }}</th>
                            <th>{{ __('umrah.phone') }}</th>
                            <th>{{ __('umrah.whatsapp') }}</th>@can('umrah_passports_view')
                            <th>{{ __('umrah.passport_location') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($travelers as $traveler)
                            <tr>
                                <td><a href="{{ route('umrah.travelers.show', $traveler->id) }}">{{ $traveler->name }}</a></td>
                                <td dir="ltr">{{ $traveler->phone }}</td>
                                <td>{{ $traveler->whatsapp_enabled ? __('umrah.notifications_enabled') : __('umrah.notifications_disabled') }}
                                </td>@can('umrah_passports_view')
                                <td>{{ $traveler->passport_location ?? __('umrah.not_recorded') }}</td>@endcan
                            </tr>
                        @empty<tr>
                            <td colspan="4">{{ __('umrah.no_travelers') }}</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>{{ $travelers->links() }}
        </div>
    </div>
    @can('umrah_bookings_manage')
        <div class="card">
            <div class="card-body">
                <div>{{ __('umrah.register_traveler') }}</div>
                <form method="POST" action="{{ route('umrah.travelers.store') }}">@csrf
                    @include('dashboard.umrah.traveler-fields', ['traveler' => null, 'formKey' => 'new-traveler'])<button
                        class="btn btn-primary">{{ __('umrah.register_traveler_action') }}</button>
                </form>
            </div>
        </div>
    @endcan
@endsection