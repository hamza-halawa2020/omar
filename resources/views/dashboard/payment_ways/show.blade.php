@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-3">
        <div class="fw-bold fs-4 mb-3 text-primary">{{ __('messages.payment_ay_ashboard') }}</div>

        <!-- Summary Cards -->
        <div class="row mb-3 g-3 fs-5">
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div class="fw-bold">{{ __('messages.current_balance') }}</div>
                        <div id="paymentWayBalance" class="fw-bold badge bg-success">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div class="fw-bold">{{ __('messages.total_transactions') }}</div>
                        <div id="paymentWayTransactions" class="fw-bold badge bg-success">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div class="fw-bold">{{ __('messages.receive_limit') }}</div>
                        <div id="paymentWayReceiveLimit" class="fw-bold badge bg-primary">0</div>
                        <div class="mt-2">
                            <span class="fw-bold">{{ __('messages.used') }}: </span>
                            <span id="paymentWayReceiveUsed" class="badge bg-danger">0</span>
                        </div>
                        <div>
                            <span class="fw-bold">{{ __('messages.remaining') }}: </span>
                            <span id="paymentWayReceiveRemaining" class="badge bg-success">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div class="fw-bold">{{ __('messages.send_limit') }}</div>
                        <div id="paymentWaySendLimit" class="fw-bold badge bg-primary">0</div>
                        <div class="mt-2">
                            <span class="fw-bold">{{ __('messages.used') }}: </span>
                            <span id="paymentWaySendUsed" class="badge bg-danger">0</span>
                        </div>
                        <div>
                            <span class="fw-bold">{{ __('messages.remaining') }}: </span>
                            <span id="paymentWaySendRemaining" class="badge bg-success">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div>
                            <div>
                                <span class="fw-bold">{{ __('messages.received') }}</span>
                                <span id="receive_amount" class="fw-bold badge bg-success">0</span>
                            </div>
                            <div>
                                <span class="fw-bold">{{ __('messages.commission') }}</span>
                                <span id="receive_commission" class="fw-bold badge bg-warning">0</span>
                            </div>
                            <div>
                                <span class="fw-bold">{{ __('messages.total') }}</span>
                                <span id="receive_total" class="fw-bold badge bg-primary">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div>
                            <div>
                                <span class="fw-bold">{{ __('messages.sent') }}</span>
                                <span id="send_amount" class="fw-bold badge bg-success">0</span>
                            </div>
                            <div>
                                <span class="fw-bold">{{ __('messages.commission') }}</span>
                                <span id="send_commission" class="fw-bold badge bg-warning">0</span>
                            </div>
                            <div>
                                <span class="fw-bold">{{ __('messages.total') }}</span>
                                <span id="send_total" class="fw-bold badge bg-primary">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm text-center border-0">
                    <div class="card-body">
                        <div class="fw-bold">{{ __('messages.grand_net') }}</div>
                        <div id="grandNet" class="fw-bold badge bg-primary">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="paymentTabs">
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#details">{{ __('messages.details') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#transactions">{{ __('messages.transactions') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#logs">{{ __('messages.changes') }}</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Details Tab -->
            <div class="tab-pane fade" id="details">
                <div class="card shadow-sm p-3 border-0">
                    <div class="card-title mb-3 text-primary">{{ __('messages.payment_way_information') }}</div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.name') }}: </strong> <span id="paymentWayName"
                                    class=""></span></p>
                            <p><strong> {{ __('messages.phone_number') }}: </strong> <span id="paymentWayPhone"
                                    class=""></span></p>
                            <p><strong>{{ __('messages.category') }}: </strong> <span id="paymentWayCategory"
                                    class=""></span></p>
                            <p><strong>{{ __('messages.sub_category') }}: </strong> <span id="paymentWaySubCategory"
                                    class=""></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.type') }}: </strong> <span id="paymentWayType"
                                    class=""></span></p>
                            <p><strong>{{ __('messages.created_by') }}: </strong> <span id="paymentWayCreator"
                                    class=""></span></p>
                            <p><strong>{{ __('messages.created_at') }}: </strong> <span id="paymentWayCreatedAt"
                                    class=""></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Tab -->
            <div class="tab-pane fade show active" id="transactions">
                <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
                    <input type="text" id="searchTransactions" class="form-control w-50"
                        placeholder="{{ __('messages.search_transactions_by_notes_or_amount...') }}">
                    <select id="filterType" class="form-control w-25">
                        <option value="">{{ __('messages.all_types') }}</option>
                        <option value="receive">{{ __('messages.receive') }}</option>
                        <option value="send">{{ __('messages.send') }}</option>
                    </select>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <button id="nextDay" class="btn btn-outline-primary">&rarr;</button>
                        <input type="text" id="dateRange" class="form-control w-auto"
                            placeholder="Select date range">
                        <button id="prevDay" class="btn btn-outline-primary">&larr;</button>
                    </div>
                </div>
                <div class="">
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                        <thead class="">
                            <tr>
                                <th class="text-center">{{ __('messages.type') }}</th>
                                <th class="text-center">{{ __('messages.amount') }}</th>
                                <th class="text-center">{{ __('messages.commission') }}</th>
                                <th class="text-center">{{ __('messages.notes') }}</th>
                                <th class="text-center">{{ __('messages.creator') }}</th>
                                <th class="text-center">{{ __('messages.attachment') }}</th>
                                <th class="text-center">{{ __('messages.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Logs Tab -->
            <div class="tab-pane fade" id="logs">
                <div class="card shadow-sm p-3 border-0">
                    <div class="card-title mb-3 text-primary">{{ __('messages.activity_logs') }}</div>
                    <ul class="list-group list-group-flush" id="logsTimeline"></ul>
                </div>
            </div>
        </div>

        <!-- Loader -->
        <div id="loader" class="text-center my-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('messages.loading...') }}</span>
            </div>
        </div>

        <!-- Alerts -->
        <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;"></div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            let id = "{{ request()->id ?? '' }}";
            if (!id) {
                $("#errorMessage").text("{{ __('messages.no_payment_way_id_provided') }}").show().fadeOut(5000);
                return;
            }

            let currentDate = new Date();
            let dateRangePicker = $("#dateRange").flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [new Date()],
                onReady: function() {
                    let today = formatDate(new Date());
                    fetchPaymentWay(today, today);
                },
                onChange: function(selectedDates) {
                    if (selectedDates.length === 2) {
                        currentDate = selectedDates[0];
                        let start = formatDate(selectedDates[0]);
                        let end = formatDate(selectedDates[1]);
                        fetchPaymentWay(start, end);
                    } else if (selectedDates.length === 1) {
                        currentDate = selectedDates[0];
                        let day = formatDate(selectedDates[0]);
                        fetchPaymentWay(day, day);
                    }
                }
            });

            $("#prevDay").on("click", function() {
                currentDate.setDate(currentDate.getDate() - 1);
                fetchDay(currentDate);
            });

            $("#nextDay").on("click", function() {
                currentDate.setDate(currentDate.getDate() + 1);
                fetchDay(currentDate);
            });

            function fetchDay(date) {
                let formatted = formatDate(date);
                dateRangePicker.setDate([formatted, formatted], true);
                fetchPaymentWay(formatted, formatted);
            }

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

            function renderPaymentWay(res) {
                let data = res.data;
                let statistics = res.statistics || {};

                $("#paymentWayName").text(data.name || '');
                $("#paymentWayType").text(data.type ? data.type.charAt(0).toUpperCase() + data.type.slice(1) : '');
                $("#paymentWayPhone").text(data.phone_number || '');
                $("#paymentWayCategory").text(data.category?.name || '');
                $("#paymentWaySubCategory").text(data.subCategory?.name || '');
                $("#paymentWayBalance").text(data.balance ? ` ${parseFloat(data.balance).toFixed(2)}` : '0');
                $("#paymentWayCreator").text(data.creator?.name || '');
                $("#paymentWayCreatedAt").text(data.created_at || '');
                $("#paymentWayTransactions").text(data.transactions?.length || 0);

                $("#paymentWayReceiveLimit").text(statistics.limits?.receive_limit || '0');
                $("#paymentWayReceiveUsed").text(statistics.limits?.receive_used || '0');
                $("#paymentWayReceiveRemaining").text(statistics.limits?.receive_remaining || '0');
                $("#paymentWaySendLimit").text(statistics.limits?.send_limit || '0');
                $("#paymentWaySendUsed").text(statistics.limits?.send_used || '0');
                $("#paymentWaySendRemaining").text(statistics.limits?.send_remaining || '0');

                $("#receive_amount").text(statistics.receive?.receive_amount || '0');
                $("#receive_commission").text(statistics.receive?.receive_commission || '0');
                $("#receive_total").text(statistics.receive?.receive_total || '0');
                $("#send_amount").text(statistics.send?.send_amount || '0');
                $("#send_commission").text(statistics.send?.send_commission || '0');
                $("#send_total").text(statistics.send?.send_total || '0');
                $("#grandNet").text(statistics.grand_net || '0');

                const translations = {
                    receive: "{{ __('messages.receive') }}",
                    send: "{{ __('messages.send') }}",
                };

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
                            <td><span class="badge bg-${tx.type === 'receive' ? 'success' : 'danger'}">${translations[tx.type] ?? tx.type}</span></td>
                            <td>${parseFloat(tx.amount).toFixed(2)}</td>
                            <td>${parseFloat(tx.commission).toFixed(2)}</td>
                            <td>${tx.notes || ''}</td>
                            <td>${tx.creator?.name || ''}</td>
                            <td>${attachmentHtml}</td>
                            <td>${tx.created_at || ''}</td>
                        </tr>
                    `;
                });
                $("#transactionsTableBody").html(txHtml);

                let logsHtml = "";
                let actionLabels = {
                    create: "{{ __('messages.create') }}",
                    update: "{{ __('messages.update') }}",
                    delete: "{{ __('messages.delete') }}"
                };
                let fieldLabels = {
                    id: "{{ __('messages.id') }}",
                    name: "{{ __('messages.name') }}",
                    type: "{{ __('messages.type') }}",
                    balance: "{{ __('messages.balance') }}",
                    created_at: "{{ __('messages.created_at') }}",
                    created_by: "{{ __('messages.created_by') }}",
                    send_limit: "{{ __('messages.send_limit') }}",
                    updated_at: "{{ __('messages.updated_at') }}",
                    category_id: "{{ __('messages.category_id') }}",
                    phone_number: "{{ __('messages.phone_number') }}",
                    receive_limit: "{{ __('messages.receive_limit') }}",
                    sub_category_id: "{{ __('messages.sub_category_id') }}",
                    creator: "{{ __('messages.creator') }}",
                    category: "{{ __('messages.category') }}",
                    sub_category: "{{ __('messages.sub_category') }}",
                };

                data.logs.forEach(log => {
                    let dataDetails = "";
                    if (log.data) {
                        dataDetails = `
                            <div class="mt-2">
                                <strong class="">{{ __('messages.changes') }}:</strong>
                                <table class="table table-bordered table-sm table bordered-table sm-table mb-0">
                                    <tbody>                                      
                                        ${Object.entries(log.data).map(([key, value]) => `<tr><td class="">${fieldLabels[key] || key}</td><td>${value || ''}</td></tr>`).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                    logsHtml += `
                        <li class="border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-${log.action === 'create' ? 'success' : log.action === 'update' ? 'warning' : 'danger'} me-2">
                                        ${actionLabels[log.action] || log.action}
                                    </span>
                                    <div class="small ">{{ __('messages.created_at') }}: ${log.created_at || ''}</div>
                                </div>
                                <span class="badge bg-secondary">{{ __('messages.logs') }} #${log.id}</span>
                            </div>
                            ${dataDetails}
                        </li>
                    `;
                });
                $("#logsTimeline").html(logsHtml);
            }

            function fetchPaymentWay(startDate = null, endDate = null) {
                $("#loader").show();
                let data = {};

                if (startDate && endDate) {
                    data.time = "custom";
                    data.start_date = startDate;
                    data.end_date = endDate;
                } else {
                    data.time = "today";
                }

                $.ajax({
                    url: `/dashboard/payment-ways/show-list/${id}`,
                    type: "GET",
                    data: data,
                    success: function(res) {
                        $("#loader").hide();
                        if (!res.status) {
                            $("#errorMessage").text(res.message || "Error fetching payment way details")
                                .show().fadeOut(5000);
                            return;
                        }
                        renderPaymentWay(res);
                    },
                    error: function(xhr) {
                        $("#loader").hide();
                        $("#errorMessage").text(xhr.responseJSON?.message ||
                            "Error fetching payment way details").show().fadeOut(5000);
                    }
                });
            }

            function formatDate(date) {
                let year = date.getFullYear();
                let month = String(date.getMonth() + 1).padStart(2, "0");
                let day = String(date.getDate()).padStart(2, "0");
                return `${year}-${month}-${day}`;
            }

            fetchDay(currentDate);
        });
    </script>
@endpush
