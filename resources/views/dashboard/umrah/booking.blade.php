@extends('dashboard.umrah.layout')
@section('umrah_title', $booking->traveler->name)
@section('umrah_back', route('umrah.trips.show', $booking->trip_id))
@section('umrah')
<div class="umrah-quick-actions">
@if(auth()->user()->hasPermissionTo('umrah_payments_collect') || auth()->user()->hasPermissionTo('umrah_refunds_approve'))<a class="btn btn-primary" href="#umrah-payment-form">{{ __('umrah.record_payment') }}</a>@endif
<a class="btn btn-outline-primary" href="{{ route('umrah.travelers.show',$booking->traveler_id) }}">{{ __('umrah.traveler_file_link') }}</a>
</div>
<nav class="umrah-section-nav no-print" aria-label="{{ __('umrah.section_navigation') }}">
<a href="#umrah-booking-summary">{{ __('umrah.overview') }}</a><a href="#umrah-booking-payments">{{ __('umrah.payments_statement') }}</a><a href="#umrah-booking-procedures">{{ __('umrah.travel_procedures') }}</a><a href="#umrah-booking-audit">{{ __('umrah.audit_log') }}</a>
</nav>
    <div class="card" id="umrah-booking-summary">
        <div class="card-body">
            <div>{{ __('umrah.booking_prefix') }} {{ $booking->traveler->name }}</div>
            <p><bdi>{{ $booking->reference }}</bdi> — {{ __('umrah.' . $booking->status) }}</p>
            <p><a href="{{ route('umrah.trips.show', $booking->trip_id) }}">{{ $booking->trip->name }}</a> — <a
                    href="{{ route('umrah.travelers.show', $booking->traveler_id) }}">{{ __('umrah.traveler_file_link') }}</a>
            </p>
            <p>{{ data_get($booking->agreement, 'program.name') }} / {{ data_get($booking->agreement, 'label') }} —
                {{ __('umrah.makkah') }}: {{ data_get($booking->agreement, 'program.makkah_hotel') }} —
                {{ __('umrah.madinah') }}: {{ data_get($booking->agreement, 'program.madinah_hotel') }}
            </p>
            <p>{{ data_get($booking->agreement, 'program.services') }}</p>
            <p>{{ __('umrah.listed_price_snapshot') }}: {{ data_get($booking->agreement, 'price') }}
                {{ $booking->currency }}
                — {{ __('umrah.agreed_amount_snapshot') }}:
                {{ data_get($booking->agreement, 'agreed_amount', data_get($booking->agreement, 'price')) }}
                {{ $booking->currency }}
            </p>
            @if(data_get($booking->agreement, 'price_adjustment_reason'))
                <p>{{ __('umrah.price_adjustment_reason') }}: {{ data_get($booking->agreement, 'price_adjustment_reason') }}</p>
            @endif
            <div class="row g-3 umrah-metrics">
                @foreach([__('umrah.due') => $booking->total_amount, __('umrah.net_paid') => \App\Support\UmrahMoney::decimal($booking->paidCents()), __('umrah.balance') => \App\Support\UmrahMoney::decimal($booking->balanceCents()), __('umrah.overdue') => \App\Support\UmrahMoney::decimal($booking->overdueCents())] as $label => $value)
                    <div class="col-md-3">
                        <div class="border rounded p-3"><small>{{ $label }}</small>
                            <div class="mt-2 mb-0">{{ $value }} {{ $booking->currency }}</div>
                        </div>
                </div>@endforeach
            </div>
            <p class="mt-3 mb-0">{{ __('umrah.balance_hint') }}</p>
        </div>
    </div>
    <div class="card" id="umrah-booking-payments">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.payments_statement') }}</div><button class="btn btn-outline-secondary no-print mb-3"
                onclick="window.print()">{{ __('umrah.print_statement') }}</button>
            <p class="text-muted">{{ __('umrah.partial_payments_help') }}</p>
            <div class="table-responsive umrah-records-wrapper">
                <table class="table umrah-records" role="table">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th role="columnheader" scope="col">{{ __('umrah.date') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.type') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.amount') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.method') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.status') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.receipt') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.review') }}</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        @forelse($booking->payments as $payment)
                            <tr role="row">
                                <td role="cell" data-label="{{ __('umrah.date') }}" data-field="date"><div class="umrah-cell-value">{{ $payment->paid_at->format('Y-m-d') }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.type') }}" data-field="type"><div class="umrah-cell-value">{{ $payment->type === 'refund' ? __('umrah.refund') : __('umrah.collection') }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.amount') }}" data-field="amount"><div class="umrah-cell-value">{{ $payment->amount }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.method') }}" data-field="method"><div class="umrah-cell-value">{{ $payment->method }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.status') }}" data-field="status"><div class="umrah-cell-value">{{ __('umrah.' . $payment->status) }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.receipt') }}" data-field="receipt"><div class="umrah-cell-value"><a
                                        href="{{ route('umrah.receipts.show', $payment->id) }}">{{ __('umrah.view_document') }}</a>
                                </div></td>
                                <td role="cell" data-label="{{ __('umrah.review') }}" data-field="review"><div class="umrah-cell-value">
                                    @can('umrah_payments_confirm')@if($payment->status === 'pending')
                                        <form method="POST"
                                            action="{{ route('umrah.payments.review', [$booking->id, $payment->id]) }}"
                                            class="d-flex gap-2">@csrf<button name="confirm" value="1"
                                                class="btn btn-sm btn-success">{{ __('umrah.confirm') }}</button><button
                                                name="confirm" value="0"
                                                class="btn btn-sm btn-outline-danger">{{ __('umrah.reject') }}</button></form>
                                    @endif @endcan
                                </div></td>
                        </tr>@empty<tr role="row">
                            <td role="cell" colspan="7">{{ __('umrah.no_payments') }}</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>
            @if(auth()->user()->hasPermissionTo('umrah_payments_collect') || auth()->user()->hasPermissionTo('umrah_refunds_approve'))
                <details id="umrah-payment-form" @if($booking->status === 'confirmed' && $booking->balanceCents() > 0) open @endif>
                    <summary>{{ __('umrah.record_payment') }}</summary>
                    <form method="POST" action="{{ route('umrah.payments.store', $booking->id) }}">@csrf<input type="hidden"
                            name="submission_token"
                            value="{{ old('submission_token', (string) \Illuminate\Support\Str::uuid()) }}">
                        @php
                            $types = [];
                            if (auth()->user()->hasPermissionTo('umrah_payments_collect'))
                                $types['collection'] = __('umrah.collection');
                            if (auth()->user()->hasPermissionTo('umrah_refunds_approve'))
                                $types['refund'] = __('umrah.actual_refund');
                            $statuses = ['pending' => __('umrah.pending')];
                            if (auth()->user()->hasPermissionTo('umrah_payments_confirm'))
                                $statuses['confirmed'] = __('umrah.confirmed');
                        @endphp
                        @include('dashboard.umrah.fields', [
                            'formKey' => 'payment',
                            'fields' => [
                                ['type', __('umrah.operation_type'), 'select', old('type', 'collection'), true, $types],
                                ['amount', __('umrah.amount') . ' - ' . $booking->currency, 'number', old('amount'), true],
                                ['method', __('umrah.payment_method'), 'text', old('method'), true],
                                ['paid_at', __('umrah.payment_date'), 'date', old('paid_at', today()->format('Y-m-d')), true],
                                ['status', __('umrah.collection_status'), 'select', old('status', 'pending'), true, $statuses],
                                ['external_reference', __('umrah.transfer_reference'), 'text', old('external_reference')],
                                ['notes', __('umrah.internal_notes'), 'textarea', old('notes')],
                            ]
                        ])<button class="btn btn-primary">{{ __('umrah.record_operation') }}</button>
                    </form>
                </details>
            @endif
        </div>
    </div>
    <div class="card" id="umrah-booking-schedule">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.payment_schedule') }}</div>
            <ul>@forelse($booking->schedules->sortBy('due_date') as $schedule)
            <li>{{ $schedule->due_date->format('Y-m-d') }} — {{ $schedule->amount }} {{ $booking->currency }}</li>@empty
                <li>{{ __('umrah.no_schedule') }}</li>@endforelse
            </ul>
            @can('umrah_bookings_manage')@if($booking->status === 'confirmed')
                <form method="POST" action="{{ route('umrah.schedules.store', $booking->id) }}">@csrf
                    @include('dashboard.umrah.fields', ['formKey' => 'schedule', 'fields' => [['amount', __('umrah.due_amount'), 'number', '', true], ['due_date', __('umrah.due_date'), 'date', '', true]]])<button
                        class="btn btn-primary">{{ __('umrah.add_schedule') }}</button>
            </form>@endif @endcan
        </div>
    </div>
    <div class="card" id="umrah-booking-procedures">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.travel_procedures') }}</div>
            <p>{{ __('umrah.visa_status') }}: {{ __('umrah.' . $booking->visa_status) }} — {{ __('umrah.ticket_status') }}:
                {{ __('umrah.' . $booking->ticket_status) }} — {{ __('umrah.travel_status') }}:
                {{ __('umrah.' . $booking->travel_status) }}
            </p>
            @can('umrah_procedures_manage')@if($booking->status === 'confirmed')
                <form method="POST" action="{{ route('umrah.procedures.update', $booking->id) }}">@csrf
                    @include('dashboard.umrah.fields', [
                        'formKey' => 'procedures',
                        'fields' => [
                            ['visa_status', __('umrah.visa_status'), 'select', $booking->visa_status, true, collect(config('umrah.visa_statuses'))->mapWithKeys(fn($s) => [$s => __('umrah.' . $s)])->all()],
                            ['ticket_status', __('umrah.ticket_status'), 'select', $booking->ticket_status, true, collect(config('umrah.ticket_statuses'))->mapWithKeys(fn($s) => [$s => __('umrah.' . $s)])->all()],
                            ['travel_status', __('umrah.travel_status'), 'select', $booking->travel_status, true, collect(config('umrah.travel_statuses'))->mapWithKeys(fn($s) => [$s => __('umrah.' . $s)])->all()],
                        ]
                    ])<button class="btn btn-primary">{{ __('umrah.save_procedures_notify') }}</button>
            </form>@endif @endcan
        </div>
    </div>
    @can('umrah_refunds_approve')@if($booking->status === 'confirmed')
        <div class="card">
            <div class="card-body">
                <details>
                    <summary>{{ __('umrah.settle_cancel') }}</summary>
                    <p>{{ __('umrah.settlement_help') }}</p>
                    <form method="POST" action="{{ route('umrah.bookings.settle', $booking->id) }}">@csrf
                        @include('dashboard.umrah.fields', ['formKey' => 'settle', 'fields' => [['total_amount', __('umrah.new_total'), 'number', $booking->total_amount, true], ['cancel', __('umrah.settlement_type'), 'select', 0, true, [0 => __('umrah.adjust_agreement'), 1 => __('umrah.cancel_settle')]], ['reason', __('umrah.settlement_reason'), 'textarea', '', true]]])<button
                            class="btn btn-outline-danger">{{ __('umrah.record_settlement') }}</button>
                    </form>
                </details>
                <details>
                    <summary>{{ __('umrah.move_program') }}</summary>
                    <p>{{ __('umrah.move_program_help') }}</p>
                    <form method="POST" action="{{ route('umrah.bookings.program', $booking->id) }}">@csrf
                        @include('dashboard.umrah.fields', [
                            'formKey' => 'change-program',
                            'fields' => [
                                ['program_price_id', __('umrah.new_tier'), 'select', $booking->program_price_id, true, $booking->trip->programs->flatMap(fn($p) => $p->prices->where('is_active', true)->where('currency', $booking->currency)->map(fn($v) => ['id' => $v->id, 'label' => $p->name . ' / ' . $v->label . ' / ' . $v->amount . ' ' . $v->currency]))->pluck('label', 'id')->all()],
                                ['reason', __('umrah.move_reason'), 'textarea', '', true],
                            ]
                        ])<button class="btn btn-outline-primary">{{ __('umrah.move_booking') }}</button>
                    </form>
                </details>
            </div>
        </div>
    @endif @endcan
    <div class="card" id="umrah-booking-audit">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.audit_log') }}</div>
            <ul>@foreach($booking->logs->sortByDesc('id') as $log)
                <li class="mb-3">{{ $log->created_at->timezone('Africa/Cairo')->format('Y-m-d H:i') }} —
                    {{ __('umrah.' . $log->action) }} — {{ __('umrah.employee') }} #{{ $log->created_by }}
                    <details>
                        <summary>{{ __('umrah.change_details') }}</summary>
                        <pre dir="auto"
                            style="white-space:pre-wrap">{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </details>
            </li>@endforeach
            </ul>
        </div>
    </div>
    @can('umrah_bookings_manage')@if($booking->status === 'confirmed' && $booking->travel_status === 'returned' && $booking->balanceCents() === 0)
        <form class="no-print" method="POST" action="{{ route('umrah.bookings.complete', $booking->id) }}">@csrf<button
                class="btn btn-success">{{ __('umrah.close_booking') }}</button></form>
    @endif @endcan
@endsection