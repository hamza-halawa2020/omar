<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('umrah.traveler') }}</th>
                <th>{{ __('umrah.program') }}</th>
                <th>{{ __('umrah.due') }}</th>
                <th>{{ __('umrah.net_paid') }}</th>
                <th>{{ __('umrah.balance') }}</th>
                <th>{{ __('umrah.overdue') }}</th>
                <th>{{ __('umrah.booking_procedures') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td><a href="{{ route('umrah.bookings.show', $booking->id) }}">{{ $booking->traveler->name }}</a></td>
                    <td>{{ data_get($booking->agreement, 'program.name') }} / {{ data_get($booking->agreement, 'label') }}
                    </td>
                    <td>{{ $booking->total_amount }} {{ $booking->currency }}</td>
                    <td>{{ \App\Support\UmrahMoney::decimal($booking->paidCents()) }}</td>
                    <td>{{ \App\Support\UmrahMoney::decimal($booking->balanceCents()) }}</td>
                    <td>{{ \App\Support\UmrahMoney::decimal($booking->overdueCents()) }}</td>
                    <td>{{ __('umrah.' . $booking->status) }} / {{ __('umrah.' . $booking->visa_status) }} /
                        {{ __('umrah.' . $booking->travel_status) }}</td>
                </tr>
            @empty<tr>
                <td colspan="7">{{ __('umrah.no_bookings') }}</td>
            </tr>@endforelse
        </tbody>
    </table>
</div>