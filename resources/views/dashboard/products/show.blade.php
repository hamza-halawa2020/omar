@extends('dashboard.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3 mobile-stack-header">
        <div class="fw-bold">{{ __('messages.product_details') }}: {{ $product->name }}</div>
    </div>

    <div class="card p-3 mb-4">
        <div class="fw-bold mb-2">{{ __('messages.basic_information') }}</div>
        <p><strong>{{ __('messages.name') }}:</strong> {{ $product->name }}</p>
        <p><strong>{{ __('messages.description') }}:</strong> {{ $product->description }}</p>
        <p><strong>{{ __('messages.sale_price') }}:</strong> {{ number_format($product->sale_price, 0) }}</p>
        <p><strong>{{ __('messages.stock') }}:</strong> {{ $product->stock }}</p>
        <p><strong>{{ __('messages.total_amount_cost') }}:</strong> {{ number_format($totalCost, 0) }}</p>
    </div>

    <div class="card p-3 mt-3">
        <div class="fw-bold mb-3">{{ __('messages.installment_contracts') }}</div>

        @if ($installmentContracts->isEmpty())
            <div class="">{{ __('messages.no_installment_contracts') }}</div>
        @else
            <div class="table-responsive responsive-records-wrapper">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                    <thead class="">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">{{ __('messages.sale_price') }}</th>
                            <th class="text-center">{{ __('messages.customer_name') }}</th>
                            <th class="text-center">{{ __('messages.total_amount') }}</th>
                            <th class="text-center">{{ __('messages.installment_amount') }}</th>
                            <th class="text-center">{{ __('messages.start_date') }}</th>
                            <th class="text-center">{{ __('messages.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($installmentContracts as $index => $contract)
                            <tr>
                                <td data-label="#">{{ $index + 1 }}</td>
                                <td data-label="{{ __('messages.sale_price') }}">{{ number_format($contract->product_price,0) ?? '' }}</td>
                                <td class="mobile-primary" data-label="{{ __('messages.customer_name') }}">{{ $contract->client->name ?? '' }}</td>
                                <td data-label="{{ __('messages.total_amount') }}">{{ number_format($contract->total_amount, 0) }}</td>
                                <td data-label="{{ __('messages.installment_amount') }}">{{ ceil($contract->installment_amount) }}</td>
                                <td data-label="{{ __('messages.start_date') }}">{{ $contract->start_date ? $contract->start_date : '' }}</td>
                                <td data-label="{{ __('messages.status') }}">
                                    @if ($contract->status == 'active')
                                        <span class="badge bg-success">{{ __('messages.active') }}</span>
                                    @elseif ($contract->status == 'completed')
                                        <span class="badge bg-primary">{{ __('messages.completed') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('messages.pending') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="card p-3 mt-3">
        <div class="fw-bold mb-3">{{ __('messages.transactions') }}</div>

        @if ($transactions->isEmpty())
            <div class="">{{ __('messages.no_transactions') }}</div>
        @else
            <div class="table-responsive responsive-records-wrapper">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                    <thead class="">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">{{ __('messages.amount') }}</th>
                            <th class="text-center">{{ __('messages.commission') }}</th>
                            <th class="text-center">{{ __('messages.quantity') }}</th>
                            <th class="text-center">{{ __('messages.sale_cost') }}</th>
                            <th class="text-center">{{ __('messages.net_profit_loss') }}</th>
                            <th class="text-center">{{ __('messages.status') }}</th>
                            <th class="text-center">{{ __('messages.notes') }}</th>
                            <th class="text-center">{{ __('messages.type') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @dd($transactions) --}}
                        @foreach ($transactions as $index => $contract)
                            <tr>
                                <td data-label="#">{{ $index + 1 }}</td>
                                <td class="mobile-primary" data-label="{{ __('messages.amount') }}">{{ number_format($contract->amount ?? 0, 2) }}</td>
                                <td data-label="{{ __('messages.commission') }}">{{ number_format($contract->commission ?? 0, 2) }}</td> 
                                <td data-label="{{ __('messages.quantity') }}">{{ $contract->quantity ?? 1 }}</td> 
                                <td data-label="{{ __('messages.sale_cost') }}">
                                    {{ $contract->type == 'receive' ? number_format($contract->sale_cost, 2) : '-' }}
                                </td>
                                <td data-label="{{ __('messages.net_profit_loss') }}" class="{{ $contract->type == 'receive' ? ($contract->sale_profit >= 0 ? 'text-success' : 'text-danger') : '' }}">
                                    {{ $contract->type == 'receive' ? number_format($contract->sale_profit, 2) : '-' }}
                                </td>
                                <td data-label="{{ __('messages.status') }}">
                                    @if ($contract->type == 'receive')
                                        @if ($contract->sale_profit > 0)
                                            <span class="badge bg-success">{{ __('messages.profit') }}</span>
                                        @elseif ($contract->sale_profit < 0)
                                            <span class="badge bg-danger">{{ __('messages.loss') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('messages.break_even') }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-info">{{ __('messages.stock_purchase') }}</span>
                                    @endif
                                </td>
                                <td class="mobile-muted" data-label="{{ __('messages.notes') }}">{{ $contract->notes ?? '' }}</td> 
                                <td data-label="{{ __('messages.type') }}">
                                    @if ($contract->type == 'receive')
                                        <span class="badge bg-success">{{ __('messages.receive') }}</span>
                                    @elseif ($contract->type == 'send')
                                        <span class="badge bg-primary">{{ __('messages.send') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
