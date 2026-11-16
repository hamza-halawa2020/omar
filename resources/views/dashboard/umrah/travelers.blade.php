@extends('dashboard.umrah.layout')
@section('umrah_title', __('umrah.travelers'))
@section('umrah')
    @can('umrah_bookings_manage')
        <div class="umrah-quick-actions"><a class="btn btn-primary"
    href="#umrah-new-traveler">{{ __('umrah.register_traveler') }}</a></div>@endcan
    <div class="card">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.travelers') }}</div>
            <form method="GET" class="d-flex gap-2 mb-3 umrah-search"><input class="form-control" name="q"
                    value="{{ $search }}" placeholder="{{ __('umrah.search_hint') }}"
                    aria-label="{{ __('umrah.search_hint') }}"><button
                    class="btn btn-outline-primary">{{ __('umrah.search') }}</button></form>
            <div class="table-responsive umrah-records-wrapper">
                <table class="table umrah-records" role="table">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th role="columnheader" scope="col">{{ __('umrah.name') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.phone') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.whatsapp') }}</th>@can('umrah_passports_view')
                            <th role="columnheader" scope="col">{{ __('umrah.passport_location') }}</th>@endcan
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        @forelse($travelers as $traveler)
                            <tr role="row">
                                <td role="cell" data-label="{{ __('umrah.name') }}" data-field="name">
                                    <div class="umrah-cell-value"><a
                                            href="{{ route('umrah.travelers.show', $traveler->id) }}">{{ $traveler->name }}</a>
                                    </div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.phone') }}" data-field="phone">
                                    <div class="umrah-cell-value" dir="ltr">{{ $traveler->phone }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.whatsapp') }}" data-field="whatsapp">
                                    <div class="umrah-cell-value">
                                        {{ $traveler->whatsapp_enabled ? __('umrah.notifications_enabled') : __('umrah.notifications_disabled') }}
                                    </div>
                                </td>@can('umrah_passports_view')
                                    <td role="cell" data-label="{{ __('umrah.passport_location') }}" data-field="passport_location">
                                        <div class="umrah-cell-value">{{ $traveler->passport_location ?? __('umrah.not_recorded') }}
                                        </div>
                                </td>@endcan
                            </tr>
                        @empty<tr role="row">
                            <td role="cell" colspan="4">{{ __('umrah.no_travelers') }}</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>{{ $travelers->links('dashboard.umrah.pagination') }}
        </div>
    </div>
    @can('umrah_bookings_manage')
        <div class="card">
            <div class="card-body">
                <details id="umrah-new-traveler" class="umrah-create-panel" @if($errors->any()) open @endif>
                    <summary>{{ __('umrah.register_traveler') }}</summary>
                    <form method="POST" action="{{ route('umrah.travelers.store') }}">@csrf
                        @include('dashboard.umrah.traveler-fields', ['traveler' => null, 'formKey' => 'new-traveler'])<button
                            class="btn btn-primary">{{ __('umrah.register_traveler_action') }}</button>
                    </form>
                </details>
            </div>
        </div>
    @endcan
@endsection