@extends('dashboard.layouts.app')

@section('content')
    @php
        $money = fn ($value) => number_format((float) $value, 2);
        $number = fn ($value) => number_format((float) $value, 0);
    @endphp

    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-3 mobile-stack-header">
            <div class="fw-bold">{{ __('messages.product_details') }}: {{ $product->name }}</div>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">{{ __('messages.products') }}</a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">{{ __('messages.stock') }}</div>
                        <div class="h4 mb-0">{{ $number($product->stock) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">{{ __('messages.total_amount_cost') }}</div>
                        <div class="h4 mb-0">{{ $money($totalCost) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">{{ __('messages.sold_quantity') }}</div>
                        <div class="h4 mb-0">{{ $number($summary['sold_quantity']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">{{ __('messages.net_profit_loss') }}</div>
                        <div class="h4 mb-0 {{ $summary['sales_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $money($summary['sales_profit']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="fw-bold mb-3">{{ __('messages.basic_information') }}</div>
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-xl-3">
                        <span class="text-muted">{{ __('messages.name') }}</span>
                        <div class="fw-semibold">{{ $product->name }}</div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <span class="text-muted">{{ __('messages.code') }}</span>
                        <div class="fw-semibold">{{ $product->code ?? '-' }}</div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <span class="text-muted">{{ __('messages.purchase_price') }}</span>
                        <div class="fw-semibold">{{ $money($product->purchase_price) }}</div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <span class="text-muted">{{ __('messages.sale_price') }}</span>
                        <div class="fw-semibold">{{ $money($product->sale_price) }}</div>
                    </div>
                    <div class="col-12">
                        <span class="text-muted">{{ __('messages.description') }}</span>
                        <div>{{ $product->description ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="fw-bold">{{ __('messages.purchase_batches') }}</div>
                    <span class="badge bg-primary">{{ $number($purchaseBatches->sum('remaining_quantity')) }} {{ __('messages.remaining') }}</span>
                </div>

                @if ($purchaseBatches->isEmpty())
                    <div>{{ __('messages.no_data_found') }}</div>
                @else
                    <div class="table-responsive responsive-records-wrapper">
                        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.purchase_price') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.remaining') }}</th>
                                    <th>{{ __('messages.total_amount_cost') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseBatches as $batch)
                                    <tr>
                                        <td data-label="#">{{ $batch->id }}</td>
                                        <td data-label="{{ __('messages.purchase_price') }}">{{ $money($batch->unit_cost) }}</td>
                                        <td data-label="{{ __('messages.quantity') }}">{{ $number($batch->purchased_quantity) }}</td>
                                        <td class="mobile-primary" data-label="{{ __('messages.remaining') }}">{{ $number($batch->remaining_quantity) }}</td>
                                        <td data-label="{{ __('messages.total_amount_cost') }}">{{ $money($batch->remaining_quantity * $batch->unit_cost) }}</td>
                                        <td class="mobile-muted" data-label="{{ __('messages.created_at') }}">{{ $batch->created_at?->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="fw-bold">{{ __('messages.sales_profit') }}</div>
                    <span class="badge bg-success">{{ $money($summary['sales_amount']) }}</span>
                </div>

                @if ($salesTransactions->isEmpty())
                    <div>{{ __('messages.no_transactions') }}</div>
                @else
                    <div class="table-responsive responsive-records-wrapper">
                        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                    <th>{{ __('messages.sale_cost') }}</th>
                                    <th>{{ __('messages.commission') }}</th>
                                    <th>{{ __('messages.net_profit_loss') }}</th>
                                    <th>{{ __('messages.batch_cost') }}</th>
                                    <th>{{ __('messages.client') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesTransactions as $transaction)
                                    @php
                                        $allocations = optional($transaction->product_line)->batchAllocations ?? collect();
                                        $batchText = $allocations->isNotEmpty()
                                            ? $allocations->map(fn ($allocation) => '#' . ($allocation->product_purchase_batch_id ?? '-') . ' x' . $allocation->quantity . ' @ ' . $money($allocation->unit_cost))->implode(' | ')
                                            : '-';
                                    @endphp
                                    <tr>
                                        <td data-label="#">{{ $transaction->id }}</td>
                                        <td data-label="{{ __('messages.quantity') }}">{{ $number($transaction->quantity) }}</td>
                                        <td class="mobile-primary" data-label="{{ __('messages.amount') }}">{{ $money(optional($transaction->product_line)->total ?? $transaction->amount) }}</td>
                                        <td data-label="{{ __('messages.sale_cost') }}">{{ $money($transaction->sale_cost) }}</td>
                                        <td data-label="{{ __('messages.commission') }}">{{ $money($transaction->commission) }}</td>
                                        <td data-label="{{ __('messages.net_profit_loss') }}" class="{{ $transaction->sale_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $money($transaction->sale_profit) }}
                                        </td>
                                        <td class="mobile-muted" data-label="{{ __('messages.batch_cost') }}">{{ $batchText }}</td>
                                        <td data-label="{{ __('messages.client') }}">{{ optional($transaction->client)->name ?? '-' }}</td>
                                        <td class="mobile-muted" data-label="{{ __('messages.created_at') }}">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="fw-bold">{{ __('messages.stock_purchase') }}</div>
                    <span class="badge bg-info">{{ $number($summary['purchased_quantity']) }} {{ __('messages.quantity') }}</span>
                </div>

                @if ($purchaseTransactions->isEmpty())
                    <div>{{ __('messages.no_transactions') }}</div>
                @else
                    <div class="table-responsive responsive-records-wrapper">
                        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.purchase_price') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                    <th>{{ __('messages.payment_way') }}</th>
                                    <th>{{ __('messages.notes') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseTransactions as $transaction)
                                    <tr>
                                        <td data-label="#">{{ $transaction->id }}</td>
                                        <td data-label="{{ __('messages.quantity') }}">{{ $number($transaction->quantity) }}</td>
                                        <td class="mobile-primary" data-label="{{ __('messages.purchase_price') }}">{{ $money(optional($transaction->product_line)->unit_price) }}</td>
                                        <td data-label="{{ __('messages.amount') }}">{{ $money(optional($transaction->product_line)->total ?? $transaction->amount) }}</td>
                                        <td data-label="{{ __('messages.payment_way') }}">{{ optional($transaction->paymentWay)->name ?? '-' }}</td>
                                        <td class="mobile-muted" data-label="{{ __('messages.notes') }}">{{ $transaction->notes ?? '-' }}</td>
                                        <td class="mobile-muted" data-label="{{ __('messages.created_at') }}">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="fw-bold mb-3">{{ __('messages.installment_contracts') }}</div>

                @if ($installmentContracts->isEmpty())
                    <div>{{ __('messages.no_installment_contracts') }}</div>
                @else
                    <div class="table-responsive responsive-records-wrapper">
                        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.sale_price') }}</th>
                                    <th>{{ __('messages.customer_name') }}</th>
                                    <th>{{ __('messages.total_amount') }}</th>
                                    <th>{{ __('messages.installment_amount') }}</th>
                                    <th>{{ __('messages.start_date') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($installmentContracts as $index => $contract)
                                    <tr>
                                        <td data-label="#">{{ $index + 1 }}</td>
                                        <td data-label="{{ __('messages.sale_price') }}">{{ $money($contract->product_price) }}</td>
                                        <td class="mobile-primary" data-label="{{ __('messages.customer_name') }}">{{ $contract->client->name ?? '-' }}</td>
                                        <td data-label="{{ __('messages.total_amount') }}">{{ $money($contract->total_amount) }}</td>
                                        <td data-label="{{ __('messages.installment_amount') }}">{{ $money($contract->installment_amount) }}</td>
                                        <td data-label="{{ __('messages.start_date') }}">{{ $contract->start_date ?: '-' }}</td>
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
        </div>
    </div>
@endsection
