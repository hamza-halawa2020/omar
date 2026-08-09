@extends('dashboard.layouts.app')

@section('title', __('messages.transactions_list'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 mobile-stack-header">
        <div>{{ __('messages.transactions_list') }}</div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('transactions.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="from_date" class="form-label">{{ __('messages.from_date') }}</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
                </div>
                <div class="col-md-4">
                    <label for="to_date" class="form-label">{{ __('messages.to_date') }}</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end mobile-filter-actions">
                    <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ms-2">{{ __('messages.reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive responsive-records-wrapper">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">{{ __('messages.type') }}</th>
                            <th class="text-center">{{ __('messages.amount') }}</th>
                            <th class="text-center">{{ __('messages.client') }}</th>
                            <th class="text-center">{{ __('messages.product') }}</th>
                            <th class="text-center">{{ __('messages.payment_way') }}</th>
                            <th class="text-center">{{ __('messages.created_by') }}</th>
                            <th class="text-center">{{ __('messages.created_at') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td data-label="{{ __('messages.id') }}">{{ $transaction->id }}</td>
                                <td class="mobile-primary" data-label="{{ __('messages.type') }}">{{ $transaction->type == 'send' ? __('messages.send') : __('messages.receive') }}</td>
                                <td data-label="{{ __('messages.amount') }}">{{ number_format($transaction->amount, 2) }}</td>
                                <td data-label="{{ __('messages.client') }}">{{ optional($transaction->client)->name ?? '-' }}</td>
                                <td class="mobile-muted mobile-hide" data-label="{{ __('messages.product') }}">{{ optional($transaction->product)->name ?? '-' }}</td>
                                <td data-label="{{ __('messages.payment_way') }}">{{ optional($transaction->paymentWay)->name ?? '-' }}</td>
                                <td class="mobile-muted mobile-hide" data-label="{{ __('messages.created_by') }}">{{ optional($transaction->creator)->name ?? '-' }}</td>
                                <td class="mobile-muted" data-label="{{ __('messages.created_at') }}">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                <td class="mobile-actions" data-label="{{ __('messages.actions') }}">
                                    @if ($transaction->paymentWay)
                                        <a href="{{ route('payment_ways.show', $transaction->paymentWay->id) }}" class="btn btn-sm btn-info">{{ __('messages.show') }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center" data-label="{{ __('messages.no_data_found') }}">{{ __('messages.no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
