@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-3">
        <div class="fw-bold fs-3 mb-3 text-primary">Payment Way Dashboard</div>

        <!-- Summary Cards -->
        <div class="row mb-3 g-3 fs-5">
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div class="fw-bold ">Current Balance</div>
                        <div id="paymentWayBalance" class="fw-bold badge bg-danger">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div class="fw-bold ">Total Transactions</div>
                        <div id="paymentWayTransactions" class="fw-bold badge bg-danger">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div id="paymentWayReceiveLimitAlert" class="text-danger"></div>
                        <span class="fw-bold">Receive Limit</span>
                        <span id="paymentWayReceiveLimit" class="fw-bold badge bg-danger">0</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div id="paymentWaySendLimitAlert" class="text-danger"></div>
                        <span class="fw-bold ">Send Limit</span>
                        <span id="paymentWaySendLimit" class="fw-bold badge bg-danger">0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="paymentTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#details">Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#transactions">Transactions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#logs">Logs</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Details Tab -->
            <div class="tab-pane fade show active" id="details">
                <div class="card shadow-sm p-3 border-0">
                    <div class="card-title mb-3 text-primary">Payment Way Information</div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <span id="paymentWayName" class=""></span></p>
                            <p><strong>Phone Number:</strong> <span id="paymentWayPhone" class=""></span></p>
                            <p><strong>Category:</strong> <span id="paymentWayCategory" class=""></span></p>
                            <p><strong>Sub Category:</strong> <span id="paymentWaySubCategory" class=""></span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Type:</strong> <span id="paymentWayType" class=""></span></p>
                            <p><strong>Created By:</strong> <span id="paymentWayCreator" class=""></span></p>
                            <p><strong>Created At:</strong> <span id="paymentWayCreatedAt" class=""></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Tab -->
            <div class="tab-pane fade" id="transactions">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                    <input type="text" id="searchTransactions" class="form-control w-50"
                        placeholder="Search transactions by notes or amount...">
                    <select id="filterType" class="form-control w-25">
                        <option value="">All Types</option>
                        <option value="receive">Receive</option>
                        <option value="send">Send</option>
                    </select>
                    <select id="timeFilter" class="form-control w-auto">
                        <option value="today" selected>Today</option>
                        <option value="week" >Week</option>
                        <option value="month">Month</option>
                        <option value="year">Year</option>
                        <option value="all">All Time</option>
                    </select>
                </div>

                <div class="">
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="text-center">Type</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Commission</th>
                                <th class="text-center">Notes</th>
                                <th class="text-center">Creator</th>
                                <th class="text-center">Attachment</th>
                                <th class="text-center">Created At</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Logs Tab -->
            <div class="tab-pane fade" id="logs">
                <div class="card shadow-sm p-3 border-0">
                    <div class="card-title mb-3 text-primary">Activity Logs</div>
                    <ul class="list-group list-group-flush" id="logsTimeline"></ul>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="text-center my-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Alerts -->
        <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;"></div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let id = "{{ request()->id ?? '' }}";
            if (!id) {
                $("#errorMessage").text("No Payment Way ID provided").show().fadeOut(5000);
                return;
            }

            // Filter Transactions
            function filterTransactions() {
                let searchText = $("#searchTransactions").val().toLowerCase();
                let filterType = $("#filterType").val();

                $("#transactionsTableBody tr").each(function() {
                    let row = $(this);
                    let type = row.find("td:first").text().toLowerCase();
                    let rowText = row.text().toLowerCase();

                    let matchesSearch = rowText.includes(searchText);
                    let matchesType = !filterType || type.includes(filterType);

                    row.toggle(matchesSearch && matchesType);
                });
            }

            $("#searchTransactions").on("keyup", filterTransactions);
            $("#filterType").on("change", filterTransactions);

            function renderPaymentWay(data) {
                // Summary Cards
                $("#paymentWayName").text(data.name || '');
                $("#paymentWayType").text(data.type ? data.type.charAt(0).toUpperCase() + data.type.slice(1) :
                    '');
                $("#paymentWayPhone").text(data.phone_number || '');
                $("#paymentWayCategory").text(data.category?.name || '');
                $("#paymentWaySubCategory").text(data.subCategory?.name || '');
                $("#paymentWaySendLimitAlert").text(data.send_limit_alert ?
                    ` ${parseFloat(data.send_limit_alert).toFixed(2)}` : '');
                $("#paymentWayReceiveLimitAlert").text(data.receive_limit_alert ?
                    ` ${parseFloat(data.receive_limit_alert).toFixed(2)}` : '');
                $("#paymentWayBalance").text(data.balance ? ` ${parseFloat(data.balance).toFixed(2)}` : '0.00');
                $("#paymentWayCreator").text(data.creator?.name || '');
                $("#paymentWayCreatedAt").text(data.created_at || '');
                $("#paymentWayReceiveLimit").text(data.receive_limit ?
                    ` ${parseFloat(data.receive_limit).toFixed(2)}` : '0.00');
                $("#paymentWaySendLimit").text(data.send_limit ? ` ${parseFloat(data.send_limit).toFixed(2)}` :
                    '0.00');
                $("#paymentWayTransactions").text(data.transactions?.length || 0);
                $("#paymentWayReceiveLimitAlert").html(data.receive_limit_alert ?
                    `<span class="badge bg-danger">Alert:  ${parseFloat(data.receive_limit_alert).toFixed(2)}</span>` :
                    '');
                $("#paymentWaySendLimitAlert").html(data.send_limit_alert ?
                    `<span class="badge bg-danger">Alert:  ${parseFloat(data.send_limit_alert).toFixed(2)}</span>` :
                    '');

                // Transactions Table
                let txHtml = "";
                data.transactions.forEach(tx => {
                    let attachmentHtml = tx.attachment ?
                        `<a href="${tx.attachment}" target="_blank" class="text-primary">View</a>` : '';
                    if (tx.attachment && /\.(jpg|jpeg|png|gif)$/i.test(tx.attachment)) {
                        attachmentHtml =
                            `<a href="${tx.attachment}" target="_blank"><img src="${tx.attachment}" alt="Attachment" class="img-thumbnail" style="max-width: 50px; max-height: 50px;"></a>`;
                    }
                    txHtml += `
                        <tr>
                            <td><span class="badge bg-${tx.type === 'receive' ? 'success' : 'danger'}">${tx.type.charAt(0).toUpperCase() + tx.type.slice(1)}</span></td>
                            <td> ${parseFloat(tx.amount).toFixed(2)}</td>
                            <td> ${parseFloat(tx.commission).toFixed(2)}</td>
                            <td>${tx.notes || ''}</td>
                            <td>${tx.creator?.name || ''}</td>
                            <td>${attachmentHtml}</td>
                            <td>${tx.created_at || ''}</td>
                        </tr>
                    `;
                });
                $("#transactionsTableBody").html(txHtml);

                // Logs Timeline
                let logsHtml = "";
                data.logs.forEach(log => {
                    let dataDetails = "";
                    if (log.data) {
                        dataDetails = `
                            <div class="mt-2">
                                <strong class="">Changes:</strong>
                                <table class="table table-sm table-bordered ">
                                    <tbody>
                                        ${Object.entries(log.data).map(([key, value]) => `
                                                            <tr>
                                                                <td class="">${key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}</td>
                                                                <td>${value || ''}</td>
                                                            </tr>
                                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                    logsHtml += `
                        <li class="list-group-item border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-${log.action === 'create' ? 'success' : log.action === 'update' ? 'warning' : 'danger'} me-2">
                                        <i class="bi bi-${log.action === 'create' ? 'plus-circle' : log.action === 'update' ? 'pencil' : 'trash'} me-1"></i>
                                        ${log.action.toUpperCase()}
                                    </span>
                                    by ${log.creator?.name || ''}
                                    <div class="small ">Created: ${log.created_at || ''}</div>
                                </div>
                                <span class="badge bg-secondary">Log #${log.id}</span>
                            </div>
                            ${dataDetails}
                        </li>
                    `;
                });
                $("#logsTimeline").html(logsHtml);
            }

            function fetchPaymentWay(time = 'today') {
                $("#loader").show();
                $.ajax({
                    url: `/dashboard/payment-ways/show-list/${id}`,
                    type: "GET",
                    data: {
                        time: time
                    },
                    success: function(res) {
                        $("#loader").hide();
                        if (!res.status) {
                            $("#errorMessage").text(res.message || "Error fetching payment way details")
                                .show().fadeOut(5000);
                            return;
                        }
                        renderPaymentWay(res.data);
                    },
                    error: function(xhr) {
                        $("#loader").hide();
                        $("#errorMessage").text(xhr.responseJSON?.message ||
                            "Error fetching payment way details").show().fadeOut(5000);
                    }
                });
            }

            // Initial fetch
            fetchPaymentWay('today');

            // Time filter change
            $("#timeFilter").on('change', function() {
                fetchPaymentWay($(this).val());
            });
        });
    </script>
@endpush
