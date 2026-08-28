@extends('dashboard.layouts.app')

@section('title', __('messages.product_sales_analysis'))

@section('content')
    @php
        $currency = __('messages.currency_symbol');
        $formatMoney = fn ($value) => number_format((float) $value, 2) . ' ' . $currency;
        $formatNumber = fn ($value) => number_format((float) $value, 0);
    @endphp

    <style>
        .product-analytics-filter {
            border: 0;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .product-analytics-filter .card-body {
            padding: 20px;
        }

        .product-analytics-filter .product-filter-grid {
            display: grid;
            grid-template-columns: minmax(140px, 1fr) minmax(140px, 1fr) minmax(260px, 2fr) minmax(220px, auto);
            gap: 16px;
            align-items: end;
        }

        .product-analytics-filter .product-filter-grid > * {
            min-width: 0;
        }

        .product-analytics-filter .filter-field {
            display: grid;
            grid-template-rows: 18px 46px;
            row-gap: 8px;
            align-content: end;
            height: 100%;
        }

        .product-analytics-filter .form-label {
            color: #334155;
            font-size: 13px;
            font-weight: 600;
            line-height: 18px;
            margin-bottom: 0;
        }

        .product-analytics-filter .form-control,
        .product-analytics-filter .form-select {
            min-height: 46px;
            border-color: #d8dee8;
            border-radius: 8px;
            box-shadow: none;
        }

        .product-analytics-filter .form-control:focus,
        .product-analytics-filter .form-select:focus,
        .product-analytics-filter .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .product-analytics-filter .product-filter-col .select2,
        .product-analytics-filter .product-filter-col .select2-container,
        .product-analytics-filter .product-filter-col .selection,
        .product-analytics-filter .product-filter-col .select2-selection {
            display: block;
            width: 100% !important;
        }

        .product-analytics-filter .product-filter-col .select2-container {
            grid-row: 2;
        }

        .product-analytics-filter .select2-container .select2-selection--single {
            position: relative;
            width: 100%;
            height: 46px;
            min-height: 46px;
            border: 1px solid #d8dee8;
            border-radius: 8px;
            overflow: hidden;
        }

        .product-analytics-filter .select2-container .select2-selection__rendered {
            display: block;
            width: 100% !important;
            line-height: 44px;
            padding-inline-start: 14px;
            padding-inline-end: 36px;
            overflow: hidden;
            text-align: right;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .product-analytics-filter .select2-container .select2-selection__arrow {
            height: 44px;
        }

        .product-analytics-filter .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__rendered {
            padding-right: 14px;
            padding-left: 64px;
        }

        .product-analytics-filter .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__arrow {
            right: auto;
            left: 10px;
            width: 20px;
        }

        .product-analytics-filter .select2-container--default[dir="rtl"] .select2-selection--single .select2-selection__clear {
            position: absolute;
            top: 50%;
            right: auto;
            left: 36px;
            float: none;
            margin: 0;
            transform: translateY(-50%);
            z-index: 2;
        }

        .product-analytics-filter .select2-search__field {
            width: 100% !important;
        }

        .product-analytics-filter .analytics-filter-actions {
            display: grid;
            grid-template-columns: minmax(120px, 1fr) minmax(110px, auto);
            gap: 10px;
        }

        .product-analytics-filter .analytics-filter-actions .btn {
            min-height: 46px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
        }

        @media (max-width: 767.98px) {
            .product-analytics-filter .product-filter-grid {
                grid-template-columns: 1fr;
            }

            .product-analytics-filter .analytics-filter-actions {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .product-analytics-filter .product-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>

    <div class="container-fluid py-3 product-analytics-page">
        <div class="d-flex justify-content-between align-items-center mb-3 mobile-stack-header">
            <div>{{ __('messages.product_sales_analysis') }}</div>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">{{ __('messages.products') }}</a>
        </div>

        <div class="card mb-3 product-analytics-filter">
            <div class="card-body">
                <form action="{{ route('products.analytics') }}" method="GET" class="product-filter-grid">
                    <div class="filter-field">
                        <label for="from_date" class="form-label">{{ __('messages.from_date') }}</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
                    </div>
                    <div class="filter-field">
                        <label for="to_date" class="form-label">{{ __('messages.to_date') }}</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
                    </div>
                    <div class="filter-field product-filter-col">
                        <label for="product_id" class="form-label">{{ __('messages.product') }}</label>
                        <select name="product_id" id="product_id" class="form-select product-search-select w-100" style="width: 100%;">
                            <option value="">{{ __('messages.all_products') }}</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected($productId === $product->id)>
                                    {{ $product->name }}{{ $product->code ? ' - ' . $product->code : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="analytics-filter-actions mobile-filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <iconify-icon icon="mdi:filter-outline"></iconify-icon>
                            {{ __('messages.filter') }}
                        </button>
                        <a href="{{ route('products.analytics') }}" class="btn btn-outline-secondary">
                            <iconify-icon icon="mdi:refresh"></iconify-icon>
                            {{ __('messages.reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">{{ __('messages.total_products_sales') }}</div>
                        <div class="h4 mb-0">{{ $formatMoney($totals['sales_amount']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">{{ __('messages.total_sales_costs') }}</div>
                        <div class="h4 mb-0">{{ $formatMoney($totals['sales_cost']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">{{ __('messages.net_profit_loss') }}</div>
                        <div class="h4 mb-0 {{ $totals['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $formatMoney($totals['net_profit']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted mb-1">{{ __('messages.profit_margin') }}</div>
                        <div class="h4 mb-0">{{ number_format($totals['profit_margin'], 2) }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="card-title mb-0">{{ __('messages.products_profit_loss') }}</div>
                            <span class="badge bg-primary">{{ $formatNumber($totals['sold_quantity']) }} {{ __('messages.quantity') }}</span>
                        </div>
                        <div style="height: 280px;">
                            <canvas id="productProfitChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="card-title">{{ __('messages.period_summary') }}</div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ __('messages.products_count') }}</span>
                            <strong>{{ $formatNumber($totals['products_count']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ __('messages.sales_count') }}</span>
                            <strong>{{ $formatNumber($totals['sales_count']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ __('messages.total_commission') }}</span>
                            <strong>{{ $formatMoney($totals['sales_commission']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ __('messages.gross_profit_before_commission') }}</span>
                            <strong class="{{ $totals['gross_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $formatMoney($totals['gross_profit']) }}
                            </strong>
                        </div>
                        {{-- <div class="d-flex justify-content-between py-2">
                            <span>{{ __('messages.stock_purchase_transactions') }}</span>
                            <strong>{{ $formatMoney($totals['purchase_amount']) }}</strong>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="card-title">{{ __('messages.product_performance') }}</div>
                <div class="table-responsive responsive-records-wrapper">
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                        <thead>
                            <tr>
                                <th>{{ __('messages.product') }}</th>
                                <th>{{ __('messages.code') }}</th>
                                <th>{{ __('messages.sold_quantity') }}</th>
                                <th>{{ __('messages.total_product_sales') }}</th>
                                <th>{{ __('messages.total_sales_cost') }}</th>
                                <th>{{ __('messages.total_commission') }}</th>
                                <th>{{ __('messages.net_profit_loss') }}</th>
                                <th>{{ __('messages.profit_margin') }}</th>
                                <th>{{ __('messages.stock') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productRows as $row)
                                <tr>
                                    <td class="mobile-primary" data-label="{{ __('messages.product') }}">{{ $row->name }}</td>
                                    <td data-label="{{ __('messages.code') }}">{{ $row->code ?? '-' }}</td>
                                    <td data-label="{{ __('messages.sold_quantity') }}">{{ $formatNumber($row->sold_quantity) }}</td>
                                    <td data-label="{{ __('messages.total_product_sales') }}">{{ $formatMoney($row->sales_amount) }}</td>
                                    <td data-label="{{ __('messages.total_sales_cost') }}">{{ $formatMoney($row->sales_cost) }}</td>
                                    <td data-label="{{ __('messages.total_commission') }}">{{ $formatMoney($row->sales_commission) }}</td>
                                    <td data-label="{{ __('messages.net_profit_loss') }}" class="{{ $row->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $formatMoney($row->net_profit) }}
                                    </td>
                                    <td data-label="{{ __('messages.profit_margin') }}">{{ number_format($row->profit_margin, 2) }}%</td>
                                    <td data-label="{{ __('messages.stock') }}">{{ $formatNumber($row->stock) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center" data-label="{{ __('messages.no_data_found') }}">{{ __('messages.no_data_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="card-title">{{ __('messages.sales_transactions_profit') }}</div>
                <div class="table-responsive responsive-records-wrapper">
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.product') }}</th>
                                <th>{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.amount') }}</th>
                                <th>{{ __('messages.sale_cost') }}</th>
                                <th>{{ __('messages.commission') }}</th>
                                <th>{{ __('messages.net_profit_loss') }}</th>
                                <th>{{ __('messages.client') }}</th>
                                <th>{{ __('messages.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salesTransactions as $transaction)
                                <tr>
                                    <td data-label="{{ __('messages.id') }}">{{ $transaction->id }}</td>
                                    <td class="mobile-primary" data-label="{{ __('messages.product') }}">{{ optional($transaction->product)->name ?? '-' }}</td>
                                    <td data-label="{{ __('messages.quantity') }}">{{ $formatNumber($transaction->analytics_quantity) }}</td>
                                    <td data-label="{{ __('messages.amount') }}">{{ $formatMoney($transaction->amount) }}</td>
                                    <td data-label="{{ __('messages.sale_cost') }}">{{ $formatMoney($transaction->analytics_cost) }}</td>
                                    <td data-label="{{ __('messages.commission') }}">{{ $formatMoney($transaction->commission) }}</td>
                                    <td data-label="{{ __('messages.net_profit_loss') }}" class="{{ $transaction->analytics_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $formatMoney($transaction->analytics_profit) }}
                                    </td>
                                    <td data-label="{{ __('messages.client') }}">{{ optional($transaction->client)->name ?? '-' }}</td>
                                    <td data-label="{{ __('messages.created_at') }}">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
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
                    {{ $salesTransactions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if ($.fn.select2) {
                const $productSelect = $('#product_id');

                $productSelect.select2({
                    width: '100%',
                    allowClear: true,
                    dropdownAutoWidth: false,
                    minimumResultsForSearch: 0,
                    placeholder: "{{ __('messages.all_products') }}",
                    dir: $('html').attr('dir') || 'rtl'
                });

                $productSelect.next('.select2-container').css({
                    width: '100%',
                    display: 'block'
                });
            }

            const chartCanvas = document.getElementById('productProfitChart');
            if (!chartCanvas) return;

            const labels = @json($productRows->take(10)->pluck('name')->values());
            const values = @json($productRows->take(10)->pluck('net_profit')->map(fn ($value) => round($value, 2))->values());

            new Chart(chartCanvas, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: '{{ __('messages.net_profit_loss') }}',
                        data: values,
                        backgroundColor: values.map(value => value >= 0 ? 'rgba(22, 163, 74, 0.75)' : 'rgba(220, 38, 38, 0.75)'),
                        borderColor: values.map(value => value >= 0 ? 'rgb(22, 163, 74)' : 'rgb(220, 38, 38)'),
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => Number(value).toLocaleString()
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
