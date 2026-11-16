@extends('dashboard.umrah.layout')
@section('umrah')
    <div class="card">
        <div class="card-body">
            <div>
                {{ $payment->status === 'confirmed' ? ($payment->type === 'refund' ? __('umrah.refund_receipt') : __('umrah.collection_receipt')) : __('umrah.unconfirmed_notice') }}
            </div>
            <p>{{ tenant('name') }}</p>
            <p>{{ __('umrah.document_number') }}: <bdi>{{ $payment->receipt_number }}</bdi></p>
            <p>{{ __('umrah.customer') }}: {{ $payment->booking->traveler->name }}</p>
            <p>{{ __('umrah.trip') }}: {{ $payment->booking->trip->name }}</p>
            <p>{{ __('umrah.booking') }}: <a
                    href="{{ route('umrah.bookings.show', $payment->booking_id) }}">{{ $payment->booking->reference }}</a>
            </p>
            <div>{{ $payment->amount }} {{ $payment->booking->currency }}</div>
            <p>{{ __('umrah.on_date') }}: {{ $payment->paid_at->format('Y-m-d') }} — {{ __('umrah.method') }}:
                {{ $payment->method }} — {{ __('umrah.status') }}: {{ __('umrah.' . $payment->status) }}
            </p>
            <p>{{ __('umrah.transfer_reference') }}: {{ $payment->external_reference ?? '—' }}</p>
            <p>{{ __('umrah.recorded_by') }}: #{{ $payment->created_by }} — {{ __('umrah.confirmed_by') }}:
                {{ $payment->confirmed_by ? '#' . $payment->confirmed_by : __('umrah.not_confirmed') }}
            </p>
            <p class="">{{ __('umrah.receipt_help') }}</p>
            <button class="btn btn-primary no-print" onclick="window.print()">{{ __('umrah.print') }}</button>
        </div>
    </div>
@endsection