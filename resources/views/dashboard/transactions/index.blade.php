@extends('dashboard.layouts.app')

@section('title', __('messages.transactions_list'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
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
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">{{ __('messages.filter') }}</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ms-2">{{ __('messages.reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
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
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->type == 'send' ? __('messages.send') : __('messages.receive') }}</td>
                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ optional($transaction->client)->name ?? '-' }}</td>
                                <td>{{ optional($transaction->product)->name ?? '-' }}</td>
                                <td>{{ optional($transaction->paymentWay)->name ?? '-' }}</td>
                                <td>{{ optional($transaction->creator)->name ?? '-' }}</td>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('payment_ways.show', $transaction->paymentWay->id) }}" class="btn btn-sm btn-info">{{ __('messages.show') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ __('messages.no_data_found') }}</td>
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
