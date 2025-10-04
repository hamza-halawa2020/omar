{{-- قسم المنتجات والمعاملات: Products, Last Send, Last Receive --}}

<!-- Top Products by Installments -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_products_by_installments') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="products">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="productsChartContainer">
                <canvas id="productsChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="productsTableContainer">
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
</div>

<!-- Last Send Transactions -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.last_send_transactions') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="lastSend">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="lastSendChartContainer">
                <canvas id="lastSendChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="lastSendTableContainer">
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
</div>

<!-- Last Receive Transactions -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.last_receive_transactions') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="lastReceive">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="lastReceiveChartContainer">
                <canvas id="lastReceiveChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="lastReceiveTableContainer">
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

@push('scripts')
    <script>
        // Chart instances for Products & Transactions Section
        let productsChart, lastSendChart, lastReceiveChart;

        // Initialize charts for Products & Transactions Section
        function initializeProductsTransactionsCharts() {
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            };

            productsChart = new Chart(document.getElementById('productsChart'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.contract_count') }}',
                        data: [],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                },
                options: chartOptions
            });

            lastSendChart = new Chart(document.getElementById('lastSendChart'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.amount') }}',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        fill: false
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: '{{ __('messages.date') }}'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('messages.amount') }}'
                            }
                        }
                    }
                }
            });

            lastReceiveChart = new Chart(document.getElementById('lastReceiveChart'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.amount') }}',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        fill: false
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: '{{ __('messages.date') }}'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('messages.amount') }}'
                            }
                        }
                    }
                }
            });
        }

        // Update Products & Transactions Section Data
        function updateProductsTransactionsSection(data) {
            // Update Top Products by Installments
            document.getElementById('top_products_by_installments').innerHTML = data.top_products_by_installments.map(
                item => `
        <tr><td>${item.name}</td><td>${item.installment_contract_count}</td></tr>
    `).join('');
            productsChart.data.labels = data.top_products_by_installments.map(item => item.name);
            productsChart.data.datasets[0].data = data.top_products_by_installments.map(item => item
                .installment_contract_count);
            productsChart.update();

            // Update Last Send Transactions
            document.getElementById('last_send_transactions').innerHTML = data.last_send_transactions.map(item => `
        <tr><td>${item.client_name}</td><td>${item.payment_way}</td><td>${parseFloat(item.amount).toFixed(2)}</td><td>${item.created_at}</td></tr>
    `).join('');
            lastSendChart.data.labels = data.last_send_transactions.map(item => item.created_at);
            lastSendChart.data.datasets[0].data = data.last_send_transactions.map(item => parseFloat(item.amount));
            lastSendChart.update();

            // Update Last Receive Transactions
            document.getElementById('last_receive_transactions').innerHTML = data.last_receive_transactions.map(item => `
        <tr><td>${item.client_name}</td><td>${item.payment_way}</td><td>${parseFloat(item.amount).toFixed(2)}</td><td>${item.created_at}</td></tr>
    `).join('');
            lastReceiveChart.data.labels = data.last_receive_transactions.map(item => item.created_at);
            lastReceiveChart.data.datasets[0].data = data.last_receive_transactions.map(item => parseFloat(item.amount));
            lastReceiveChart.update();
        }
    </script>
@endpush
