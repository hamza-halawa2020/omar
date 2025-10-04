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
                <div class="col-md-3">
                    <label for="filter_type" class="form-label">{{ __('messages.filter_type') }}</label>
                    <select id="filter_type" name="filter_type" class="form-select">
                        <option value="today">{{ __('messages.today') }}</option>
                        <option value="month">{{ __('messages.this_month') }}</option>
                        <option value="custom">{{ __('messages.custom_range') }}</option>
                    </select>
                </div>
                <div class="col-md-3 custom-date d-none">
                    <label for="start_date" class="form-label">{{ __('messages.start_date') }}</label>
                    <input type="date" id="start_date" name="start_date" class="form-control">
                </div>
                <div class="col-md-3 custom-date d-none">
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.total_revenue') }}</div>
                    <p class="card-text display-3" id="total_revenue">0.00</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.total_overdue_amount') }}</div>
                    <p class="card-text display-3" id="total_overdue_amount">0.00</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Sections -->
    <div class="row">
        <!-- Top Clients by Debt -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('messages.top_clients_by_debt') }}</div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#debtChartCollapse" aria-expanded="false" aria-controls="debtChartCollapse">
                        {{ __('messages.toggle_chart') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="debtChartCollapse">
                        <canvas id="debtChart" height="200"></canvas>
                    </div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.debt_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_clients_by_debt"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Clients by Send Transactions -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('messages.top_clients_by_send_transactions') }}</div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#sendTransactionsChartCollapse" aria-expanded="false" aria-controls="sendTransactionsChartCollapse">
                        {{ __('messages.toggle_chart') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="sendTransactionsChartCollapse">
                        <canvas id="sendTransactionsChart" height="200"></canvas>
                    </div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.transaction_count') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_clients_by_send_transactions"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Clients by Receive Transactions -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('messages.top_clients_by_receive_transactions') }}</div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#receiveTransactionsChartCollapse" aria-expanded="false" aria-controls="receiveTransactionsChartCollapse">
                        {{ __('messages.toggle_chart') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="receiveTransactionsChartCollapse">
                        <canvas id="receiveTransactionsChart" height="200"></canvas>
                    </div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.transaction_count') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_clients_by_receive_transactions"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Clients by Installments -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.top_clients_by_installments') }}</div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.installment_count') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_clients_by_installments"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Overdue Installments -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.top_overdue_installments') }}</div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.client') }}</th>
                                <th class="text-center">{{ __('messages.due_date') }}</th>
                                <th class="text-center">{{ __('messages.overdue_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_overdue_installments"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming Installments -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.upcoming_installments') }}</div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.client') }}</th>
                                <th class="text-center">{{ __('messages.due_date') }}</th>
                                <th class="text-center">{{ __('messages.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody id="upcoming_installments"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Payment Ways by Send -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('messages.top_payment_ways_by_send') }}</div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#paymentWaysSendChartCollapse" aria-expanded="false" aria-controls="paymentWaysSendChartCollapse">
                        {{ __('messages.toggle_chart') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="paymentWaysSendChartCollapse">
                        <canvas id="paymentWaysSendChart" height="200"></canvas>
                    </div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.transaction_count') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_payment_ways_by_send"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Payment Ways by Receive -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('messages.top_payment_ways_by_receive') }}</div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#paymentWaysReceiveChartCollapse" aria-expanded="false" aria-controls="paymentWaysReceiveChartCollapse">
                        {{ __('messages.toggle_chart') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="paymentWaysReceiveChartCollapse">
                        <canvas id="paymentWaysReceiveChart" height="200"></canvas>
                    </div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.transaction_count') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_payment_ways_by_receive"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Payment Ways by Balance -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('messages.top_payment_ways_by_balance') }}</div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#paymentWaysBalanceChartCollapse" aria-expanded="false" aria-controls="paymentWaysBalanceChartCollapse">
                        {{ __('messages.toggle_chart') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="paymentWaysBalanceChartCollapse">
                        <canvas id="paymentWaysBalanceChart" height="200"></canvas>
                    </div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.balance') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_payment_ways_by_balance"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Payment Ways Nearing Send Limit -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.top_payment_ways_nearing_send_limit') }}</div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.send_used') }}</th>
                                <th class="text-center">{{ __('messages.send_limit') }}</th>
                                <th class="text-center">{{ __('messages.percentage_used') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_payment_ways_nearing_send_limit"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Payment Ways Nearing Receive Limit -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.top_payment_ways_nearing_receive_limit') }}</div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.receive_used') }}</th>
                                <th class="text-center">{{ __('messages.receive_limit') }}</th>
                                <th class="text-center">{{ __('messages.percentage_used') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_payment_ways_nearing_receive_limit"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Products by Installments -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('messages.top_products_by_installments') }}</div>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#productsChartCollapse" aria-expanded="false" aria-controls="productsChartCollapse">
                        {{ __('messages.toggle_chart') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="collapse" id="productsChartCollapse">
                        <canvas id="productsChart" height="200"></canvas>
                    </div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.name') }}</th>
                                <th class="text-center">{{ __('messages.contract_count') }}</th>
                            </tr>
                        </thead>
                        <tbody id="top_products_by_installments"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Last Send Transactions -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.last_send_transactions') }}</div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.client') }}</th>
                                <th class="text-center">{{ __('messages.payment_way') }}</th>
                                <th class="text-center">{{ __('messages.amount') }}</th>
                                <th class="text-center">{{ __('messages.date') }}</th>
                            </tr>
                        </thead>
                        <tbody id="last_send_transactions"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Last Receive Transactions -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">{{ __('messages.last_receive_transactions') }}</div>
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('messages.client') }}</th>
                                <th class="text-center">{{ __('messages.payment_way') }}</th>
                                <th class="text-center">{{ __('messages.amount') }}</th>
                                <th class="text-center">{{ __('messages.date') }}</th>
                            </tr>
                        </thead>
                        <tbody id="last_receive_transactions"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterTypeSelect = document.getElementById('filter_type');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const filterForm = document.getElementById('filterForm');
    const customDateFields = document.querySelectorAll('.custom-date');

    // Chart instances
    let debtChart, sendTransactionsChart, receiveTransactionsChart, paymentWaysSendChart, paymentWaysReceiveChart, paymentWaysBalanceChart, productsChart;

    // Toggle custom date inputs
    filterTypeSelect.addEventListener('change', function () {
        if (this.value === 'custom') {
            customDateFields.forEach(field => field.classList.remove('d-none'));
        } else {
            customDateFields.forEach(field => field.classList.add('d-none'));
            startDateInput.value = '';
            endDateInput.value = '';
        }
    });

    // Initialize charts
    function initializeCharts() {
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            }
        };

        debtChart = new Chart(document.getElementById('debtChart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: '{{ __('messages.debt_amount') }}', data: [], backgroundColor: 'rgba(75, 192, 192, 0.5)' }] },
            options: { ...chartOptions, scales: { y: { beginAtZero: true, title: { display: true, text: '{{ __('messages.debt_amount') }}' } } } }
        });

        sendTransactionsChart = new Chart(document.getElementById('sendTransactionsChart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: '{{ __('messages.transaction_count') }}', data: [], backgroundColor: 'rgba(54, 162, 235, 0.5)' }] },
            options: { ...chartOptions, scales: { y: { beginAtZero: true, title: { display: true, text: '{{ __('messages.transaction_count') }}' } } } }
        });

        receiveTransactionsChart = new Chart(document.getElementById('receiveTransactionsChart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: '{{ __('messages.transaction_count') }}', data: [], backgroundColor: 'rgba(255, 99, 132, 0.5)' }] },
            options: { ...chartOptions, scales: { y: { beginAtZero: true, title: { display: true, text: '{{ __('messages.transaction_count') }}' } } } }
        });

        paymentWaysSendChart = new Chart(document.getElementById('paymentWaysSendChart'), {
            type: 'pie',
            data: { labels: [], datasets: [{ label: '{{ __('messages.transaction_count') }}', data: [], backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'] }] },
            options: chartOptions
        });

        paymentWaysReceiveChart = new Chart(document.getElementById('paymentWaysReceiveChart'), {
            type: 'pie',
            data: { labels: [], datasets: [{ label: '{{ __('messages.transaction_count') }}', data: [], backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'] }] },
            options: chartOptions
        });

        paymentWaysBalanceChart = new Chart(document.getElementById('paymentWaysBalanceChart'), {
            type: 'bar',
            data: { labels: [], datasets: [{ label: '{{ __('messages.balance') }}', data: [], backgroundColor: 'rgba(153, 102, 255, 0.5)' }] },
            options: { ...chartOptions, scales: { y: { beginAtZero: true, title: { display: true, text: '{{ __('messages.balance') }}' } } } }
        });

        productsChart = new Chart(document.getElementById('productsChart'), {
            type: 'pie',
            data: { labels: [], datasets: [{ label: '{{ __('messages.contract_count') }}', data: [], backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'] }] },
            options: chartOptions
        });
    }

    // Fetch statistics
    function fetchStatistics() {
        const params = new URLSearchParams({
            filter_type: filterTypeSelect.value,
            ...(filterTypeSelect.value === 'custom' && {
                start_date: startDateInput.value,
                end_date: endDateInput.value
            })
        });

        axios.get('{{ route("dashboard.analytics") }}', { params })
            .then(response => {
                const data = response.data.statistics;

                // Update Total Revenue and Overdue Amount
                document.getElementById('total_revenue').textContent = parseFloat(data.total_revenue).toFixed(2);
                document.getElementById('total_overdue_amount').textContent = parseFloat(data.total_overdue_amount).toFixed(2);

                // Update Top Clients by Debt
                document.getElementById('top_clients_by_debt').innerHTML = data.top_clients_by_debt.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${parseFloat(item.total_remaining_amount).toFixed(2)}</td>
                    </tr>
                `).join('');
                debtChart.data.labels = data.top_clients_by_debt.map(item => item.name);
                debtChart.data.datasets[0].data = data.top_clients_by_debt.map(item => parseFloat(item.total_remaining_amount));
                debtChart.update();

                // Update Top Clients by Send Transactions
                document.getElementById('top_clients_by_send_transactions').innerHTML = data.top_clients_by_send_transactions.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.transaction_count}</td>
                    </tr>
                `).join('');
                sendTransactionsChart.data.labels = data.top_clients_by_send_transactions.map(item => item.name);
                sendTransactionsChart.data.datasets[0].data = data.top_clients_by_send_transactions.map(item => item.transaction_count);
                sendTransactionsChart.update();

                // Update Top Clients by Receive Transactions
                document.getElementById('top_clients_by_receive_transactions').innerHTML = data.top_clients_by_receive_transactions.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.transaction_count}</td>
                    </tr>
                `).join('');
                receiveTransactionsChart.data.labels = data.top_clients_by_receive_transactions.map(item => item.name);
                receiveTransactionsChart.data.datasets[0].data = data.top_clients_by_receive_transactions.map(item => item.transaction_count);
                receiveTransactionsChart.update();

                // Update Top Clients by Installments
                document.getElementById('top_clients_by_installments').innerHTML = data.top_clients_by_installments.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.installment_count}</td>
                    </tr>
                `).join('');

                // Update Top Overdue Installments
                document.getElementById('top_overdue_installments').innerHTML = data.top_overdue_installments.map(item => `
                    <tr>
                        <td>${item.client_name}</td>
                        <td>${item.due_date}</td>
                        <td>${parseFloat(item.overdue_amount).toFixed(2)}</td>
                    </tr>
                `).join('');

                // Update Upcoming Installments
                document.getElementById('upcoming_installments').innerHTML = data.upcoming_installments.map(item => `
                    <tr>
                        <td>${item.client_name}</td>
                        <td>${item.due_date}</td>
                        <td>${parseFloat(item.required_amount).toFixed(2)}</td>
                    </tr>
                `).join('');

                // Update Top Payment Ways by Send
                document.getElementById('top_payment_ways_by_send').innerHTML = data.top_payment_ways_by_send.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.transaction_count}</td>
                    </tr>
                `).join('');
                paymentWaysSendChart.data.labels = data.top_payment_ways_by_send.map(item => item.name);
                paymentWaysSendChart.data.datasets[0].data = data.top_payment_ways_by_send.map(item => item.transaction_count);
                paymentWaysSendChart.update();

                // Update Top Payment Ways by Receive
                document.getElementById('top_payment_ways_by_receive').innerHTML = data.top_payment_ways_by_receive.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.transaction_count}</td>
                    </tr>
                `).join('');
                paymentWaysReceiveChart.data.labels = data.top_payment_ways_by_receive.map(item => item.name);
                paymentWaysReceiveChart.data.datasets[0].data = data.top_payment_ways_by_receive.map(item => item.transaction_count);
                paymentWaysReceiveChart.update();

                // Update Top Payment Ways by Balance
                document.getElementById('top_payment_ways_by_balance').innerHTML = data.top_payment_ways_by_balance.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${parseFloat(item.balance).toFixed(2)}</td>
                    </tr>
                `).join('');
                paymentWaysBalanceChart.data.labels = data.top_payment_ways_by_balance.map(item => item.name);
                paymentWaysBalanceChart.data.datasets[0].data = data.top_payment_ways_by_balance.map(item => parseFloat(item.balance));
                paymentWaysBalanceChart.update();

                // Update Top Payment Ways Nearing Send Limit
                document.getElementById('top_payment_ways_nearing_send_limit').innerHTML = data.top_payment_ways_nearing_send_limit.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${parseFloat(item.send_used).toFixed(2)}</td>
                        <td>${parseFloat(item.send_limit).toFixed(2)}</td>
                        <td>${parseFloat(item.percentage_used).toFixed(2)}%</td>
                    </tr>
                `).join('');

                // Update Top Payment Ways Nearing Receive Limit
                document.getElementById('top_payment_ways_nearing_receive_limit').innerHTML = data.top_payment_ways_nearing_receive_limit.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${parseFloat(item.receive_used).toFixed(2)}</td>
                        <td>${parseFloat(item.receive_limit).toFixed(2)}</td>
                        <td>${parseFloat(item.percentage_used).toFixed(2)}%</td>
                    </tr>
                `).join('');

                // Update Top Products by Installments
                document.getElementById('top_products_by_installments').innerHTML = data.top_products_by_installments.map(item => `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.installment_contract_count}</td>
                    </tr>
                `).join('');
                productsChart.data.labels = data.top_products_by_installments.map(item => item.name);
                productsChart.data.datasets[0].data = data.top_products_by_installments.map(item => item.installment_contract_count);
                productsChart.update();

                // Update Last Send Transactions
                document.getElementById('last_send_transactions').innerHTML = data.last_send_transactions.map(item => `
                    <tr>
                        <td>${item.client_name}</td>
                        <td>${item.payment_way}</td>
                        <td>${parseFloat(item.amount).toFixed(2)}</td>
                        <td>${item.created_at}</td>
                    </tr>
                `).join('');

                // Update Last Receive Transactions
                document.getElementById('last_receive_transactions').innerHTML = data.last_receive_transactions.map(item => `
                    <tr>
                        <td>${item.client_name}</td>
                        <td>${item.payment_way}</td>
                        <td>${parseFloat(item.amount).toFixed(2)}</td>
                        <td>${item.created_at}</td>
                    </tr>
                `).join('');
            })
            .catch(error => {
                console.error('Error fetching statistics:', error);
                alert('{{ __('messages.error_fetching_statistics') }}');
            });
    }

    // Initialize charts and fetch statistics
    initializeCharts();
    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchStatistics();
    });

    // Initial fetch
    fetchStatistics();
});
</script>
@endpush