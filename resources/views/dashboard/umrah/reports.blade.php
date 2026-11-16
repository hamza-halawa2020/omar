@extends('dashboard.umrah.layout')
@section('umrah_title', __('umrah.reports'))
@section('umrah')
    <div class="card">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.ledger_reports') }}</div>
            <p>{{ __('umrah.reports_help') }}</p>
            <form method="GET">
                @include('dashboard.umrah.fields', [
                    'formKey' => 'report',
                    'fields' => [
                        ['trip_id', __('umrah.trip'), 'select', $filters['trip_id'] ?? '', false, ['' => __('umrah.all_trips')] + $trips->pluck('name', 'id')->all()],
                        ['currency', __('umrah.currency'), 'select', $filters['currency'] ?? '', false, ['' => __('umrah.all_currencies'), 'EGP' => __('umrah.currency_egp'), 'SAR' => __('umrah.currency_sar'), 'USD' => __('umrah.currency_usd')]],
                        ['from', __('umrah.collections_from'), 'date', $filters['from'] ?? ''],
                        ['to', __('umrah.collections_to'), 'date', $filters['to'] ?? ''],
                    ]
                ])<button class="btn btn-primary">{{ __('umrah.view_report') }}</button><button type="button"
                    onclick="window.print()" class="btn btn-outline-secondary">{{ __('umrah.print') }}</button>
            </form>
        </div>
    </div>
    @foreach($bookings->groupBy('currency') as $currency => $group)
        <div class="card">
            <div class="card-body">
                <div>{{ __('umrah.summary') }} {{ $currency }}</div>
                <div class="row g-3 umrah-metrics">
                    @foreach([
                            __('umrah.total_due') => $group->sum(fn($b) => \App\Support\UmrahMoney::cents($b->total_amount)),
                            __('umrah.net_collected') => $group->sum(fn($b) => $b->paidCents()),
                            __('umrah.owed_by_customers') => $group->sum(fn($b) => max(0, $b->balanceCents())),
                            __('umrah.owed_to_customers') => $group->sum(fn($b) => max(0, -$b->balanceCents())),
                            __('umrah.arrears') => $group->sum(fn($b) => $b->overdueCents()),
                        ] as $label => $amount)
                        <div class="col-md-4">
                            <div class="border rounded p-3">{{ $label }}
                                <div class="mt-2">{{ \App\Support\UmrahMoney::decimal($amount) }}</div>
                            </div>
                    </div>@endforeach
                </div>
            </div>
        </div>
    @endforeach
    <div class="card">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.receivables_statement') }}</div>
            @include('dashboard.umrah.booking-table')
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.confirmed_period_payments') }}</div>
            @foreach($payments->groupBy(fn($p) => $p->booking->currency) as $currency => $group)
                <p>{{ $currency }}: {{ __('umrah.collection') }}
                    {{ \App\Support\UmrahMoney::decimal($group->where('type', 'collection')->sum(fn($p) => \App\Support\UmrahMoney::cents($p->amount))) }}
                    — {{ __('umrah.refund') }}
                    {{ \App\Support\UmrahMoney::decimal($group->where('type', 'refund')->sum(fn($p) => \App\Support\UmrahMoney::cents($p->amount))) }}
            </p>@endforeach
            <div class="table-responsive umrah-records-wrapper">
                <table class="table umrah-records" role="table">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th role="columnheader" scope="col">{{ __('umrah.traveler') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.date') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.type') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.amount') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.method') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.receipt') }}</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        @forelse($payments as $payment)
                            <tr role="row">
                                <td role="cell" data-label="{{ __('umrah.traveler') }}" data-field="traveler">
                                    <div class="umrah-cell-value">{{ $payment->booking->traveler->name }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.date') }}" data-field="date">
                                    <div class="umrah-cell-value">{{ $payment->paid_at->format('Y-m-d') }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.type') }}" data-field="type">
                                    <div class="umrah-cell-value">
                                        {{ $payment->type === 'refund' ? __('umrah.refund') : __('umrah.collection') }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.amount') }}" data-field="amount">
                                    <div class="umrah-cell-value">{{ $payment->amount }} {{ $payment->booking->currency }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.method') }}" data-field="method">
                                    <div class="umrah-cell-value">{{ $payment->method }}</div>
                                </td>
                                <td role="cell" data-label="{{ __('umrah.receipt') }}" data-field="receipt">
                                    <div class="umrah-cell-value"><a
                                            href="{{ route('umrah.receipts.show', $payment->id) }}">{{ __('umrah.view') }}</a>
                                    </div>
                                </td>
                        </tr>@empty<tr role="row">
                            <td role="cell" colspan="6">{{ __('umrah.no_period_payments') }}</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection