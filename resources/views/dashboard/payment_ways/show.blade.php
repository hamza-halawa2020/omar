@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-3">
        <div class="mb-3 fw-bold fs-5">Payment Way Details & Logs</div>


        <div class="mb-3 d-flex align-items-center gap-3">

            <select id="timeFilter" class="form-control">
                <option value="today">Today</option>
                <option value="week" selected>Week</option>
                <option value="month">Month</option>
                <option value="year">Year</option>
                <option value="all">All Time</option>
            </select>

        </div>

        <!-- Loader -->
        <div id="loader" class="text-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>




        <!-- Details Card -->
        <div id="paymentWayDetails" class="card shadow-sm d-none mb-4">
            <div class="card-body">
                <h5 class="card-title" id="paymentWayName"></h5>
                <p class="card-text"><strong>Type:</strong> <span id="paymentWayType"></span></p>
                <p class="card-text"><strong>Phone:</strong> <span id="paymentWayPhone"></span></p>
                <p class="card-text"><strong>Receive Limit:</strong> <span id="paymentWayReceiveLimit"></span></p>
                <p class="card-text"><strong>Receive Limit Alert:</strong> <span id="paymentWayReceiveLimitAlert"></span></p>
                <p class="card-text"><strong>Send Limit:</strong> <span id="paymentWaySendLimit"></span></p>
                <p class="card-text"><strong>Send Limit Alert:</strong> <span id="paymentWaySendLimitAlert"></span></p>
                <p class="card-text"><strong>Balance:</strong> <span id="paymentWayBalance"></span></p>
                <p class="card-text"><strong>Created By:</strong> <span id="paymentWayCreator"></span></p>
                <p class="card-text"><strong>Transactions Count:</strong> <span id="paymentWayTransactions"></span></p>
            </div>
        </div>


        <!-- Logs Container -->
        <div id="logsContainer" class="d-none">
            <!-- Logs will be appended here -->
        </div>

        <div id="transactionsContainer" class="d-none mb-4">
            <div class="mb-3 fw-bold fs-5">Transactions</div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let id = "{{ request()->id ?? '' }}";
            if (!id) return alert("No Payment Way ID provided");

            let timeFilter = 'week'; // default

            function renderPaymentWay(data) {
                // Show details
                $("#paymentWayDetails").removeClass('d-none');
                $("#paymentWayName").text(data.name);
                $("#paymentWayType").text(data.type);
                $("#paymentWayPhone").text(data.phone_number ?? 'N/A');
                $("#paymentWayReceiveLimit").text(data.receive_limit ?? 0);
                $("#paymentWayReceiveLimitAlert").text(data.receive_limit_alert ?? 0);
                $("#paymentWaySendLimit").text(data.send_limit ?? 0);
                $("#paymentWaySendLimitAlert").text(data.send_limit_alert ?? 0);
                $("#paymentWayBalance").text(data.balance ?? 0);
                $("#paymentWayCreator").text(data.creator?.name ?? 'N/A');
                $("#paymentWayTransactions").text(data.transactions?.length ?? 0);

                // Show transactions
                $("#transactionsContainer").removeClass('d-none');
                let transactionsHtml = '';
                data.transactions.forEach(tx => {
                    transactionsHtml += `
                <div class="card mb-2 shadow-sm">
                    <div class="card-header bg-${tx.type === 'receive' ? 'success' : 'danger'} text-white">${tx.type.toUpperCase()} Created at: ${tx.created_at}</div>
                    <div class="card-body p-3">
                        <p><strong>Amount:</strong> ${tx.amount}</p>
                        <p><strong>Commission:</strong> ${tx.commission}</p>
                        <p><strong>Notes:</strong> ${tx.notes}</p>
                        ${tx.attachment ? `<p><strong>Attachment:</strong> <a href="/${tx.attachment}" target="_blank">View</a></p>` : ''}
                    </div>
                </div>
            `;
                });
                $("#transactionsContainer").html(transactionsHtml);

                // Show logs
                $("#logsContainer").removeClass('d-none');
                let logsHtml = '';
                data.logs.forEach(log => {
                    logsHtml += `
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-${log.action === 'create' ? 'success' : log.action === 'update' ? 'warning' : 'danger'} text-white">
                    <strong>${log.action.toUpperCase()}</strong> by ${log.creator?.name ?? 'N/A'} at ${log.created_at}
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm mb-0">
                        <tbody>
                            ${log.data.name ? `<tr><th>Name:</th><td>${log.data.name}</td></tr>` : ''}
                            ${log.data.type ? `<tr><th>Type:</th><td>${log.data.type}</td></tr>` : ''}
                            ${log.data.phone_number ? `<tr><th>Phone:</th><td>${log.data.phone_number}</td></tr>` : ''}
                            ${log.data.receive_limit ? `<tr><th>Receive Limit:</th><td>${log.data.receive_limit}</td></tr>` : ''}
                            ${log.data.send_limit ? `<tr><th>Send Limit:</th><td>${log.data.send_limit}</td></tr>` : ''}
                            ${log.data.balance ? `<tr><th>Balance:</th><td>${log.data.balance}</td></tr>` : ''}
                        </tbody>
                    </table>
                </div>
            </div>
            `;
                });
                $("#logsContainer").html(logsHtml);
            }

            function fetchPaymentWay() {
                $("#loader").show();
                $.ajax({
                    url: `/dashboard/payment-ways/show-list/${id}`,
                    type: "GET",
                    data: {
                        time: timeFilter
                    },
                    success: function(res) {
                        $("#loader").hide();
                        if (!res.status) return;
                        renderPaymentWay(res.data);
                    },
                    error: function() {
                        $("#loader").hide();
                        alert("Error fetching payment way details");
                    }
                });
            }

            $("#timeFilter").on('change', function() {
                timeFilter = $(this).val();
                fetchPaymentWay();
            });

            fetchPaymentWay();
        });
    </script>
@endpush
