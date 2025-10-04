@extends('dashboard.layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
    <div class="container-fluid py-3">
        <!-- Header -->
        <div class="mb-3">{{ __('messages.dashboard') }}</div>

        <!-- Date Filter Form -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="card-title">{{ __('messages.filter_statistics') }}</div>
                <form id="filterForm" class="row g-3">
                    <div class="col-md-auto">
                        <label for="filter_type" class="form-label">{{ __('messages.filter_type') }}</label>
                        <select id="filter_type" name="filter_type" class="form-select">
                            <option value="today">{{ __('messages.today') }}</option>
                            <option value="month">{{ __('messages.this_month') }}</option>
                            <option value="custom">{{ __('messages.custom_range') }}</option>
                        </select>
                    </div>
                    <div class="col-md-auto custom-date d-none">
                        <label for="start_date" class="form-label">{{ __('messages.start_date') }}</label>
                        <input type="date" id="start_date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-auto custom-date d-none">
                        <label for="end_date" class="form-label">{{ __('messages.end_date') }}</label>
                        <input type="date" id="end_date" name="end_date" class="form-control">
                    </div>
                    <div class="col-md-2 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">{{ __('messages.apply_filter') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-3">
            <div class="col-md-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">{{ __('messages.total_revenue') }}</div>
                        <p class="card-text display-3" id="total_revenue">0.00</p>
                    </div>
                </div>
            </div>

            <div class="col-md-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">{{ __('messages.total_payment_ways_balance') }}</div>
                        <p class="card-text display-3" id="total_payment_ways_balance">0.00</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            @include('dashboard.partials.clients-section')
            @include('dashboard.partials.payment-ways-section')
            @include('dashboard.partials.products-transactions-section')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterTypeSelect = document.getElementById('filter_type');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const filterForm = document.getElementById('filterForm');
            const customDateFields = document.querySelectorAll('.custom-date');

            // Toggle custom date inputs
            filterTypeSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customDateFields.forEach(field => field.classList.remove('d-none'));
                } else {
                    customDateFields.forEach(field => field.classList.add('d-none'));
                    startDateInput.value = '';
                    endDateInput.value = '';
                }
            });

            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    const chartContainer = document.getElementById(target + 'ChartContainer');
                    const tableContainer = document.getElementById(target + 'TableContainer');

                    if (chartContainer.classList.contains('d-none')) {
                        chartContainer.classList.remove('d-none');
                        tableContainer.classList.add('d-none');
                        this.textContent = '{{ __('messages.show_table') }}';
                    } else {
                        chartContainer.classList.add('d-none');
                        tableContainer.classList.remove('d-none');
                        this.textContent = '{{ __('messages.show_chart') }}';
                    }
                });
            });

            function fetchStatistics() {
                const params = new URLSearchParams({
                    filter_type: filterTypeSelect.value,
                    ...(filterTypeSelect.value === 'custom' && {
                        start_date: startDateInput.value,
                        end_date: endDateInput.value
                    })
                });

                axios.get('{{ route('dashboard.analytics') }}', {
                        params
                    })
                    .then(response => {
                        const data = response.data.statistics;

                        // Update Summary Cards (عام)
                        document.getElementById('total_revenue').textContent = parseFloat(data.total_revenue ||
                            0).toFixed(2);
                        document.getElementById('total_payment_ways_balance').textContent = parseFloat(data
                            .total_payment_ways_balance || 0).toFixed(2);

                        if (typeof updateClientsSection === 'function') updateClientsSection(data);
                        if (typeof updatePaymentWaysSection === 'function') updatePaymentWaysSection(data);
                        if (typeof updateProductsTransactionsSection === 'function')
                            updateProductsTransactionsSection(data);
                    })
                    .catch(error => {
                        console.error('Error fetching statistics:', error);
                        showToast('{{ __('messages.error_fetching_statistics') }}', 'error');
                    });
            }

            if (typeof initializeClientsCharts === 'function') initializeClientsCharts();
            if (typeof initializePaymentWaysCharts === 'function') initializePaymentWaysCharts();
            if (typeof initializeProductsTransactionsCharts === 'function') initializeProductsTransactionsCharts();

            // Form submit and initial fetch
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchStatistics();
            });
            fetchStatistics();
        });
    </script>
@endpush
