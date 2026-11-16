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
            <div class="table-responsive responsive-records-wrapper">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('umrah.traveler') }}</th>
                            <th class="text-center">{{ __('umrah.date') }}</th>
                            <th class="text-center">{{ __('umrah.type') }}</th>
                            <th class="text-center">{{ __('umrah.amount') }}</th>
                            <th class="text-center">{{ __('umrah.method') }}</th>
                            <th class="text-center">{{ __('umrah.receipt') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="mobile-primary" data-label="{{ __('umrah.traveler') }}">{{ $payment->booking->traveler->name }}</td>
                                <td data-label="{{ __('umrah.date') }}">{{ $payment->paid_at->format('Y-m-d') }}</td>
                                <td data-label="{{ __('umrah.type') }}">{{ $payment->type === 'refund' ? __('umrah.refund') : __('umrah.collection') }}</td>
                                <td data-label="{{ __('umrah.amount') }}">{{ $payment->amount }} {{ $payment->booking->currency }}</td>
                                <td class="mobile-muted" data-label="{{ __('umrah.method') }}">{{ $payment->method }}</td>
                                <td data-label="{{ __('umrah.receipt') }}"><a href="{{ route('umrah.receipts.show', $payment->id) }}">{{ __('umrah.view') }}</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('umrah.no_period_payments') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection