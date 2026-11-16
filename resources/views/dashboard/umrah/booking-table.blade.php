<div class="table-responsive responsive-records-wrapper">
    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
        <thead>
            <tr>
                <th class="text-center">{{ __('umrah.traveler') }}</th>
                <th class="text-center">{{ __('umrah.program') }}</th>
                <th class="text-center">{{ __('umrah.due') }}</th>
                <th class="text-center">{{ __('umrah.net_paid') }}</th>
                <th class="text-center">{{ __('umrah.balance') }}</th>
                <th class="text-center">{{ __('umrah.overdue') }}</th>
                <th class="text-center">{{ __('umrah.booking_procedures') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td class="mobile-primary" data-label="{{ __('umrah.traveler') }}">
                        <a href="{{ route('umrah.bookings.show', $booking->id) }}">{{ $booking->traveler->name }}</a>
                    </td>
                    <td data-label="{{ __('umrah.program') }}">
                        {{ data_get($booking->agreement, 'program.name') }} / {{ data_get($booking->agreement, 'label') }}
                    </td>
                    <td data-label="{{ __('umrah.due') }}">{{ $booking->total_amount }} {{ $booking->currency }}</td>
                    <td data-label="{{ __('umrah.net_paid') }}">{{ \App\Support\UmrahMoney::decimal($booking->paidCents()) }}</td>
                    <td data-label="{{ __('umrah.balance') }}">{{ \App\Support\UmrahMoney::decimal($booking->balanceCents()) }}</td>
                    <td data-label="{{ __('umrah.overdue') }}">{{ \App\Support\UmrahMoney::decimal($booking->overdueCents()) }}</td>
                    <td class="mobile-muted" data-label="{{ __('umrah.booking_procedures') }}">
                        {{ __('umrah.' . $booking->status) }} / {{ __('umrah.' . $booking->visa_status) }} / {{ __('umrah.' . $booking->travel_status) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">{{ __('umrah.no_bookings') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
