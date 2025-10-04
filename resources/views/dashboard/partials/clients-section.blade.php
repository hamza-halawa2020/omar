{{-- قسم العملاء: Debt, Installments, Overdue, Upcoming --}}

<!-- Top Clients by Debt -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_clients_by_debt') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="debt">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="debtChartContainer">
                <canvas id="debtChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="debtTableContainer">
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
</div>

<!-- Top Clients by Installments -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_clients_by_installments') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="installments">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="installmentsChartContainer">
                <canvas id="installmentsChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="installmentsTableContainer">
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
</div>

<!-- Top Overdue Installments -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.top_overdue_installments') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="overdue">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="overdueChartContainer">
                <canvas id="overdueChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="overdueTableContainer">
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
</div>

<!-- Upcoming Installments -->
<div class="col-md-auto mb-3">
    <div class="card">
        <div class="card-header">
            <div class="card-title">{{ __('messages.upcoming_installments') }}</div>
            <button class="btn btn-sm btn-outline-primary toggle-btn" type="button" data-target="upcoming">
                {{ __('messages.show_table') }}
            </button>
        </div>
        <div class="card-body">
            <div class="chart-container" id="upcomingChartContainer">
                <canvas id="upcomingChart" height="200"></canvas>
            </div>
            <div class="table-container d-none" id="upcomingTableContainer">
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
</div>

@push('scripts')
    <script>
        // Chart instances for Clients Section
        let clientsDebtChart, clientsInstallmentsChart, clientsOverdueChart, clientsUpcomingChart;

        // Initialize charts for Clients Section
        function initializeClientsCharts() {
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            };

            clientsDebtChart = new Chart(document.getElementById('debtChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.debt_amount') }}',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.5)'
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('messages.debt_amount') }}'
                            }
                        }
                    }
                }
            });

            clientsInstallmentsChart = new Chart(document.getElementById('installmentsChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.installment_count') }}',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.5)'
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('messages.installment_count') }}'
                            }
                        }
                    }
                }
            });

            clientsOverdueChart = new Chart(document.getElementById('overdueChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.overdue_amount') }}',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.5)'
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('messages.overdue_amount') }}'
                            }
                        }
                    }
                }
            });

            clientsUpcomingChart = new Chart(document.getElementById('upcomingChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: '{{ __('messages.amount') }}',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.5)'
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
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

        // Update Clients Section Data
        function updateClientsSection(data) {
            // Update Top Clients by Debt
            document.getElementById('top_clients_by_debt').innerHTML = data.top_clients_by_debt.map(item => `
        <tr><td>${item.name}</td><td>${parseFloat(item.total_remaining_amount).toFixed(2)}</td></tr>
    `).join('');
            clientsDebtChart.data.labels = data.top_clients_by_debt.map(item => item.name);
            clientsDebtChart.data.datasets[0].data = data.top_clients_by_debt.map(item => parseFloat(item
                .total_remaining_amount));
            clientsDebtChart.update();

            // Update Top Clients by Installments
            document.getElementById('top_clients_by_installments').innerHTML = data.top_clients_by_installments.map(item => `
        <tr><td>${item.name}</td><td>${item.installment_count}</td></tr>
    `).join('');
            clientsInstallmentsChart.data.labels = data.top_clients_by_installments.map(item => item.name);
            clientsInstallmentsChart.data.datasets[0].data = data.top_clients_by_installments.map(item => item
                .installment_count);
            clientsInstallmentsChart.update();

            // Update Top Overdue Installments
            document.getElementById('top_overdue_installments').innerHTML = data.top_overdue_installments.map(item => `
        <tr><td>${item.client_name}</td><td>${item.due_date}</td><td>${parseFloat(item.overdue_amount).toFixed(2)}</td></tr>
    `).join('');
            clientsOverdueChart.data.labels = data.top_overdue_installments.map(item => item.client_name);
            clientsOverdueChart.data.datasets[0].data = data.top_overdue_installments.map(item => parseFloat(item
                .overdue_amount));
            clientsOverdueChart.update();

            // Update Upcoming Installments
            document.getElementById('upcoming_installments').innerHTML = data.upcoming_installments.map(item => `
        <tr><td>${item.client_name}</td><td>${item.due_date}</td><td>${parseFloat(item.required_amount).toFixed(2)}</td></tr>
    `).join('');
            clientsUpcomingChart.data.labels = data.upcoming_installments.map(item => item.client_name);
            clientsUpcomingChart.data.datasets[0].data = data.upcoming_installments.map(item => parseFloat(item
                .required_amount));
            clientsUpcomingChart.update();
        }
    </script>
@endpush
