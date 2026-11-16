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
            <div class="table-responsive responsive-records-wrapper">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('umrah.name') }}</th>
                            <th class="text-center">{{ __('umrah.phone') }}</th>
                            <th class="text-center">{{ __('umrah.whatsapp') }}</th>
                            @can('umrah_passports_view')
                                <th class="text-center">{{ __('umrah.passport_location') }}</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($travelers as $traveler)
                            <tr>
                                <td class="mobile-primary" data-label="{{ __('umrah.name') }}">
                                    <a href="{{ route('umrah.travelers.show', $traveler->id) }}">{{ $traveler->name }}</a>
                                </td>
                                <td data-label="{{ __('umrah.phone') }}" dir="ltr">{{ $traveler->phone }}</td>
                                <td data-label="{{ __('umrah.whatsapp') }}">
                                    {{ $traveler->whatsapp_enabled ? __('umrah.notifications_enabled') : __('umrah.notifications_disabled') }}
                                </td>
                                @can('umrah_passports_view')
                                    <td data-label="{{ __('umrah.passport_location') }}">{{ $traveler->passport_location ?? __('umrah.not_recorded') }}</td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('umrah.no_travelers') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-table-pagination id="travelersPagination" :paginator="$travelers" />
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