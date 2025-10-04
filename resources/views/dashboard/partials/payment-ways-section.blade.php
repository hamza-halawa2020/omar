{{-- قسم طرق الدفع: Send, Receive, Balance, Nearing Limits --}}

<!-- Top Payment Ways by Send -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_payment_ways_by_send') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="paymentWaysSend">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="paymentWaysSendChartContainer">
                <canvas id="paymentWaysSendChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="paymentWaysSendTableContainer">
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
</div>

<!-- Top Payment Ways by Receive -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_payment_ways_by_receive') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="paymentWaysReceive">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="paymentWaysReceiveChartContainer">
                <canvas id="paymentWaysReceiveChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="paymentWaysReceiveTableContainer">
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
</div>

<!-- Top Payment Ways by Balance -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_payment_ways_by_balance') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="paymentWaysBalance">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="paymentWaysBalanceChartContainer">
                <canvas id="paymentWaysBalanceChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="paymentWaysBalanceTableContainer">
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
</div>

<!-- Top Payment Ways Nearing Send Limit -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_payment_ways_nearing_send_limit') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="sendLimit">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="sendLimitChartContainer">
                <canvas id="sendLimitChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="sendLimitTableContainer">
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
</div>

<!-- Top Payment Ways Nearing Receive Limit -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_payment_ways_nearing_receive_limit') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="receiveLimit">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="receiveLimitChartContainer">
                <canvas id="receiveLimitChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="receiveLimitTableContainer">
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
</div>

@push('scripts')
    <script>
        // Chart instances for Payment Ways Section
        let paymentSendChart, paymentReceiveChart, paymentBalanceChart, paymentSendLimitChart, paymentReceiveLimitChart;

        // Initialize charts for Payment Ways Section
        function initializePaymentWaysCharts() {
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            };

            paymentSendChart = new Chart(document.getElementById('paymentWaysSendChart'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.transaction_count') }}',
                        data: [],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                },
                options: chartOptions
            });

            paymentReceiveChart = new Chart(document.getElementById('paymentWaysReceiveChart'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.transaction_count') }}',
                        data: [],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                },
                options: chartOptions
            });

            paymentBalanceChart = new Chart(document.getElementById('paymentWaysBalanceChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.balance') }}',
                        data: [],
                        backgroundColor: 'rgba(153, 102, 255, 0.5)'
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('messages.balance') }}'
                            }
                        }
                    }
                }
            });

            paymentSendLimitChart = new Chart(document.getElementById('sendLimitChart'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.percentage_used') }}',
                        data: [],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                },
                options: chartOptions
            });

            paymentReceiveLimitChart = new Chart(document.getElementById('receiveLimitChart'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.percentage_used') }}',
                        data: [],
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                },
                options: chartOptions
            });
        }

        // Update Payment Ways Section Data
        function updatePaymentWaysSection(data) {
            // Update Top Payment Ways by Send
            document.getElementById('top_payment_ways_by_send').innerHTML = data.top_payment_ways_by_send.map(item => `
        <tr><td>${item.name}</td><td>${item.transaction_count}</td></tr>
    `).join('');
            paymentSendChart.data.labels = data.top_payment_ways_by_send.map(item => item.name);
            paymentSendChart.data.datasets[0].data = data.top_payment_ways_by_send.map(item => item.transaction_count);
            paymentSendChart.update();

            // Update Top Payment Ways by Receive
            document.getElementById('top_payment_ways_by_receive').innerHTML = data.top_payment_ways_by_receive.map(item => `
        <tr><td>${item.name}</td><td>${item.transaction_count}</td></tr>
    `).join('');
            paymentReceiveChart.data.labels = data.top_payment_ways_by_receive.map(item => item.name);
            paymentReceiveChart.data.datasets[0].data = data.top_payment_ways_by_receive.map(item => item
            .transaction_count);
            paymentReceiveChart.update();

            // Update Top Payment Ways by Balance
            document.getElementById('top_payment_ways_by_balance').innerHTML = data.top_payment_ways_by_balance.map(item => `
        <tr><td>${item.name}</td><td>${parseFloat(item.balance).toFixed(2)}</td></tr>
    `).join('');
            paymentBalanceChart.data.labels = data.top_payment_ways_by_balance.map(item => item.name);
            paymentBalanceChart.data.datasets[0].data = data.top_payment_ways_by_balance.map(item => parseFloat(item
                .balance));
            paymentBalanceChart.update();

            // Update Top Payment Ways Nearing Send Limit
            document.getElementById('top_payment_ways_nearing_send_limit').innerHTML = data
                .top_payment_ways_nearing_send_limit.map(item => `
        <tr><td>${item.name}</td><td>${parseFloat(item.send_used).toFixed(2)}</td><td>${parseFloat(item.send_limit).toFixed(2)}</td><td>${parseFloat(item.percentage_used).toFixed(2)}%</td></tr>
    `).join('');
            paymentSendLimitChart.data.labels = data.top_payment_ways_nearing_send_limit.map(item => item.name);
            paymentSendLimitChart.data.datasets[0].data = data.top_payment_ways_nearing_send_limit.map(item => parseFloat(
                item.percentage_used));
            paymentSendLimitChart.update();

            // Update Top Payment Ways Nearing Receive Limit
            document.getElementById('top_payment_ways_nearing_receive_limit').innerHTML = data
                .top_payment_ways_nearing_receive_limit.map(item => `
        <tr><td>${item.name}</td><td>${parseFloat(item.receive_used).toFixed(2)}</td><td>${parseFloat(item.receive_limit).toFixed(2)}</td><td>${parseFloat(item.percentage_used).toFixed(2)}%</td></tr>
    `).join('');
            paymentReceiveLimitChart.data.labels = data.top_payment_ways_nearing_receive_limit.map(item => item.name);
            paymentReceiveLimitChart.data.datasets[0].data = data.top_payment_ways_nearing_receive_limit.map(item =>
                parseFloat(item.percentage_used));
            paymentReceiveLimitChart.update();
        }
    </script>
@endpush
