<div class="table-responsive umrah-records-wrapper">
    <table class="table umrah-records" role="table">
        <thead role="rowgroup">
            <tr role="row">
                <th role="columnheader" scope="col">{{ __('umrah.traveler') }}</th>
                <th role="columnheader" scope="col">{{ __('umrah.program') }}</th>
                <th role="columnheader" scope="col">{{ __('umrah.due') }}</th>
                <th role="columnheader" scope="col">{{ __('umrah.net_paid') }}</th>
                <th role="columnheader" scope="col">{{ __('umrah.balance') }}</th>
                <th role="columnheader" scope="col">{{ __('umrah.overdue') }}</th>
                <th role="columnheader" scope="col">{{ __('umrah.booking_procedures') }}</th>
            </tr>
        </thead>
        <tbody role="rowgroup">
            @forelse($bookings as $booking)
                <tr role="row">
                    <td role="cell" data-label="{{ __('umrah.traveler') }}" data-field="traveler">
                        <div class="umrah-cell-value"><a
                                href="{{ route('umrah.bookings.show', $booking->id) }}">{{ $booking->traveler->name }}</a>
                        </div>
                    </td>
                    <td role="cell" data-label="{{ __('umrah.program') }}" data-field="program">
                        <div class="umrah-cell-value">{{ data_get($booking->agreement, 'program.name') }} /
                            {{ data_get($booking->agreement, 'label') }}
                        </div>
                    </td>
                    <td role="cell" data-label="{{ __('umrah.due') }}" data-field="due">
                        <div class="umrah-cell-value">{{ $booking->total_amount }} {{ $booking->currency }}</div>
                    </td>
                    <td role="cell" data-label="{{ __('umrah.net_paid') }}" data-field="net_paid">
                        <div class="umrah-cell-value">{{ \App\Support\UmrahMoney::decimal($booking->paidCents()) }}</div>
                    </td>
                    <td role="cell" data-label="{{ __('umrah.balance') }}" data-field="balance">
                        <div class="umrah-cell-value">{{ \App\Support\UmrahMoney::decimal($booking->balanceCents()) }}</div>
                    </td>
                    <td role="cell" data-label="{{ __('umrah.overdue') }}" data-field="overdue">
                        <div class="umrah-cell-value">{{ \App\Support\UmrahMoney::decimal($booking->overdueCents()) }}</div>
                    </td>
                    <td role="cell" data-label="{{ __('umrah.booking_procedures') }}" data-field="booking_procedures">
                        <div class="umrah-cell-value">{{ __('umrah.' . $booking->status) }} /
                            {{ __('umrah.' . $booking->visa_status) }} /
                            {{ __('umrah.' . $booking->travel_status) }}
                        </div>
                    </td>
                </tr>
            @empty<tr role="row">
                <td role="cell" colspan="7">{{ __('umrah.no_bookings') }}</td>
            </tr>@endforelse
        </tbody>
    </table>
</div>