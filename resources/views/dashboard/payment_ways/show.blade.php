@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-3">
        <div class="fw-bold fs-5 mb-3">Payment Way Dashboard</div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <div class="fw-bold">Balance</div>
                        <div id="paymentWayBalance" class="fs-4 text-primary">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <div class="fw-bold">Transactions</div>
                        <div id="paymentWayTransactions" class="fs-4 text-success">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <div class="fw-bold">Receive Limit</div>
                        <div id="paymentWayReceiveLimit" class="fs-5">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <div class="fw-bold">Send Limit</div>
                        <div id="paymentWaySendLimit" class="fs-5">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="paymentTabs">
            <li class="nav-item">
                <a class="nav-link " data-bs-toggle="tab" href="#details">Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#transactions">Transactions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#logs">Logs</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Details Tab -->
            <div class="tab-pane fade" id="details">
                <div class="card shadow-sm p-3">
                    <p><strong>Name:</strong> <span id="paymentWayName"></span></p>
                    <p><strong>Type:</strong> <span id="paymentWayType"></span></p>
                    <p><strong>Phone:</strong> <span id="paymentWayPhone"></span></p>
                    <p><strong>Created By:</strong> <span id="paymentWayCreator"></span></p>
                </div>
            </div>


            <!-- Transactions Tab -->
            <div class="tab-pane fade show active" id="transactions">
                <div class="d-flex justify-content-stretch align-items-center mb-3 gap-3">
                    <input type="text" id="searchTransactions" class="form-control w-50"
                        placeholder="Search transactions...">
                    <select id="filterType" class="form-control w-25">
                        <option value="">All</option>
                        <option value="receive">Receive</option>
                        <option value="send">Send</option>
                    </select>

                    <div>
                        <select id="timeFilter" class="form-control w-auto">
                            <option value="today">Today</option>
                            <option value="week" selected>Week</option>
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>
                </div>


                <table class="table table-bordered table-sm table bordered-table sm-table mb-0">
                    <thead class="">
                        <tr>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Commission</th>
                            <th>Notes</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody"></tbody>
                </table>
            </div>


            <!-- Logs Tab -->
            <div class="tab-pane fade" id="logs">
                <ul class="list-group" id="logsTimeline"></ul>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="text-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {




            let id = "{{ request()->id ?? '' }}";
            if (!id) return alert("No Payment Way ID provided");


            // فلترة وبحث
            function filterTransactions() {
                let searchText = $("#searchTransactions").val().toLowerCase();
                let filterType = $("#filterType").val();

                $("#transactionsTableBody tr").each(function() {
                    let row = $(this);
                    let type = row.find("td:first").text().toLowerCase();
                    let rowText = row.text().toLowerCase();

                    let matchesSearch = rowText.includes(searchText);
                    let matchesType = !filterType || type.includes(filterType);

                    if (matchesSearch && matchesType) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            }

            $("#searchTransactions").on("keyup", filterTransactions);
            $("#filterType").on("change", filterTransactions);


            function renderPaymentWay(data) {
                $("#paymentWayName").text(data.name);
                $("#paymentWayType").text(data.type);
                $("#paymentWayPhone").text(data.phone_number ?? 'N/A');
                $("#paymentWayBalance").text(data.balance ?? 0);
                $("#paymentWayCreator").text(data.creator?.name ?? 'N/A');
                $("#paymentWayReceiveLimit").text(data.receive_limit ?? 0);
                $("#paymentWaySendLimit").text(data.send_limit ?? 0);
                $("#paymentWayTransactions").text(data.transactions?.length ?? 0);

                // Transactions
                let txHtml = "";
                data.transactions.forEach(tx => {
                    txHtml += `
                <tr>
                    <td><span class="badge bg-${tx.type === 'receive' ? 'success' : 'danger'}">${tx.type}</span></td>
                    <td>${tx.amount}</td>
                    <td>${tx.commission}</td>
                    <td>${tx.notes ?? ''}</td>
                    <td>${tx.created_at}</td>
                </tr>
            `;
                });
                $("#transactionsTableBody").html(txHtml);

                // Logs
                let logsHtml = "";
                data.logs.forEach(log => {
                    logsHtml += `
                <li class="list-group-item">
                    <strong class="text-${log.action === 'create' ? 'success' : log.action === 'update' ? 'warning' : 'danger'}">
                        ${log.action.toUpperCase()}
                    </strong>
                    by ${log.creator?.name ?? 'N/A'}
                    <div class="small text-muted">${log.created_at}</div>
                </li>
            `;
                });
                $("#logsTimeline").html(logsHtml);
            }

            function fetchPaymentWay() {
                $("#loader").show();
                $.ajax({
                    url: `/dashboard/payment-ways/show-list/${id}`,
                    type: "GET",
                    data: {
                        time: 'week'
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

            fetchPaymentWay();


            function fetchTransactions(time = 'week') {
                $("#loader").removeClass('d-none');
                $("#transactionsTableBody").empty();

                $.ajax({
                    url: "{{ route('transactions.list') }}",
                    type: "GET",
                    data: {
                        time: time
                    },
                    success: function(res) {
                        $("#loader").addClass('d-none');
                        if (!res.status) return;

                        let rows = "";
                        res.data.forEach(tx => {
                            rows += `
                        <tr>
                            <td><span class="badge bg-${tx.type === 'receive' ? 'success' : 'danger'}">${tx.type}</span></td>
                            <td>${tx.amount}</td>
                            <td>${tx.commission}</td>
                            <td>${tx.payment_way?.name ?? ''}</td>
                            <td>${tx.creator?.name ?? ''}</td>
                            <td>${tx.created_at}</td>
                        </tr>
                    `;
                        });
                        $("#transactionsTableBody").html(rows);
                    },
                    error: function() {
                        $("#loader").addClass('d-none');
                        alert("Error fetching transactions");
                    }
                });
            }

            // default
            fetchTransactions();

            // on change
            $("#timeFilter").on('change', function() {
                fetchTransactions($(this).val());
            });

        });
    </script>
@endpush
