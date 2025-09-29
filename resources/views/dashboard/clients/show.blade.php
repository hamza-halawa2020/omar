@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-bold fs-5">
                {{ __('messages.client_details') }}
            </div>

        </div>

        <!-- Client Info Card -->
        <div class="card shadow-sm rounded-3 border-0 mb-3">
            <div class="card-header">
                <div class="mb-0">{{ __('messages.client_info') }}</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('messages.name') }}:</strong> <span id="clientName" class=""></span></p>
                        <p><strong>{{ __('messages.phone_number') }}:</strong> <span id="clientPhone" class=""></span>
                        </p>
                        <p><strong>{{ __('messages.debt') }}:</strong> <span id="clientDebt" class="text-success"></span>
                        </p>
                        <p><strong>{{ __('messages.created_by') }}:</strong> <span id="clientCreator" class=""></span>
                        </p>
                        <p><strong>{{ __('messages.created_at') }}:</strong> <span id="clientCreatedAt"
                                class=""></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card shadow-sm rounded-3 border-0 mb-3">
            <div class="card-header bg-success ">
                <div class="mb-0">{{ __('messages.statistics') }}</div>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3 rounded">
                            <h6>{{ __('messages.total_transactions') }}</h6>
                            <p id="totalTransactions" class="fw-bold">0</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded">
                            <h6>{{ __('messages.total_sent') }}</h6>
                            <p id="totalSent" class="fw-bold text-danger">0.00</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded">
                            <h6>{{ __('messages.total_received') }}</h6>
                            <p id="totalReceived" class="fw-bold text-success">0.00</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded">
                            <h6>{{ __('messages.total_commission') }}</h6>
                            <p id="totalCommission" class="fw-bold text-warning">0.00</p>
                        </div>
                    </div>
                </div>
                <!-- Chart -->
                <div class="mt-3">
                    <canvas id="transactionsChart"></canvas>
                </div>

            </div>
        </div>

        <!-- Transactions Card -->
        <div class="card shadow-sm rounded-3 border-0 mt-3">
            <div class="card-header  d-flex justify-content-between align-items-center">
                <div class="mb-0">{{ __('messages.transactions') }}</div>
            </div>
            <div class="card-body">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('messages.id') }}</th>
                            <th class="text-center">{{ __('messages.type') }}</th>
                            <th class="text-center">{{ __('messages.amount') }}</th>
                            <th class="text-center">{{ __('messages.commission') }}</th>
                            <th class="text-center">{{ __('messages.payment_way') }}</th>
                            <th class="text-center">{{ __('messages.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTable">
                        {{-- Data via AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Contracts & Installments -->
        <div class="accordion mt-3" id="contractsAccordion">
            {{-- dynamic --}}
        </div>

    </div>
@endsection

<style>
    #transactionsChart {
        max-width: 500px;
        max-height: 500px;
        margin: 0 auto;
    }
</style>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load client details
            function loadClientDetails() {
                $.get("{{ route('clients.showPage', $client->id) }}", function(res) {
                    if (res.status) {
                        let client = res.data;

                        // Update client info
                        $('#clientName').text(client.name);
                        $('#clientPhone').text(client.phone_number || '{{ __('messages.unknown') }}');
                        $('#clientDebt').text(parseFloat(client.debt || 0).toFixed(2));
                        $('#clientCreator').text(client.creator ? client.creator.name : '{{ __('messages.unknown') }}');
                        $('#clientCreatedAt').text(client.created_at);


                        // Update statistics
                        let totalTransactions = client.transactions.length;
                        let totalSent = client.transactions.filter(t => t.type === 'send').reduce((sum,
                            t) => sum + parseFloat(t.amount || 0), 0).toFixed(2);
                        let totalReceived = client.transactions.filter(t => t.type === 'receive').reduce((
                            sum, t) => sum + parseFloat(t.amount || 0), 0).toFixed(2);
                        let totalCommission = client.transactions.reduce((sum, t) => sum + parseFloat(t
                            .commission || 0), 0).toFixed(2);

                        $('#totalTransactions').text(totalTransactions);
                        $('#totalSent').text(totalSent);
                        $('#totalReceived').text(totalReceived);
                        $('#totalCommission').text(totalCommission);

                        // Update transactions table
                        let transactionsHtml = '';
                        let status = {
                            receive: "{{ __('messages.receive') }}",
                            send: "{{ __('messages.send') }}",
                        };
                        client.transactions.forEach(function(transaction) {
                            transactionsHtml += `
                                <tr>
                                    <td>${transaction.id}</td>
                                    <td class="${transaction.type === 'send' ? 'text-danger' : 'text-success'}">${status[transaction.type]}</td>
                                    <td>${parseFloat(transaction.amount).toFixed(2)}</td>
                                    <td>${parseFloat(transaction.commission || 0).toFixed(2)}</td>
                                    <td>${transaction.paymentWay ? transaction.paymentWay.name : '{{ __('messages.unknown') }}'}</td>
                                    <td>${transaction.created_at}</td>
                                </tr>
                            `;
                        });
                        $('#transactionsTable').html(transactionsHtml);

                        // Update chart
                        updateChart(client.transactions);


                        // العقود
                        let contractsAccordion = '';
                        client.installment_contracts.forEach(function(contract, index) {
                            let installmentsHtml = '';
                            if (contract.installments) {
                                contract.installments.forEach(function(installment) {
                                    installmentsHtml += `
                                <tr>
                                    <td>${installment.id}</td>
                                    <td>${installment.due_date}</td>
                                    <td>${parseFloat(installment.required_amount).toFixed(2)}</td>
                                    <td>${parseFloat(installment.paid_amount).toFixed(2)}</td>
                                    <td class="${installment.status === 'paid' ? 'text-success' : installment.status === 'late' ? 'text-danger' : 'text-warning'}">
                                        ${installment.status}
                                    </td>
                                </tr>
                            `;
                                });
                            }

                            contractsAccordion += `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading${index}">
                                <button class=" text-primary accordion-button ${index > 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}">
                                    {{ __('messages.installments') }} #${contract.id} - {{ __('messages.total') }} ${parseFloat(contract.total_amount).toFixed(2)}
                                </button>
                            </h2>
                            <div id="collapse${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" data-bs-parent="#contractsAccordion">
                                <div class="accordion-body">
                                    <p><strong>{{ __('messages.installment_count') }}:</strong> ${contract.installment_count}</p>
                                    <p><strong>{{ __('messages.installment_amount') }}:</strong> ${parseFloat(contract.installment_amount).toFixed(2)}</p>
                                    <hr>
                                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">{{ __('messages.id') }}</th>
                                                <th class="text-center">{{ __('messages.due_date') }}</th>
                                                <th class="text-center">{{ __('messages.required') }}</th>
                                                <th class="text-center">{{ __('messages.paid') }}</th>
                                                <th class="text-center">{{ __('messages.status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${installmentsHtml || `<tr><td colspan="5">{{ __('messages.no_installments') }}</td></tr>`}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                        });

                        $('#contractsAccordion').html(contractsAccordion);


                    } else {
                        showToast('{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            }

            // Chart.js setup
            let transactionsChart;

            function updateChart(transactions) {
                let sendCount = transactions.filter(t => t.type === 'send').length;
                let receiveCount = transactions.filter(t => t.type === 'receive').length;

                if (transactionsChart) {
                    transactionsChart.destroy();
                }

                transactionsChart = new Chart(document.getElementById('transactionsChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['{{ __('messages.send') }}', '{{ __('messages.receive') }}'],
                        datasets: [{
                            data: [sendCount, receiveCount],
                            backgroundColor: ['#dc3545', '#28a745'],
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: '{{ __('messages.transactions_distribution') }}'
                            }
                        }
                    }
                });
            }
            // Load data on page load
            loadClientDetails();
        });
    </script>
@endpush
