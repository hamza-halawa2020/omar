@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">
        <div class="d-flex justify-content-between mb-3 mobile-stack-header">
            <div class="fw-bold fs-5">{{ __('messages.iphones') }}</div>
            @can('iphones_store')
                <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal"
                    data-bs-target="#createModal">{{ __('messages.add_iphone') }}</button>
            @endcan
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>{{ __('messages.search') }}</label>
                        <input type="text" id="searchInput" class="form-control mt-1" placeholder="{{ __('messages.search') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="responsive-records-wrapper table-responsive">
            <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records" id="iphonesTable">
                <thead>
                    <tr>
                        <th class="text-center">{{ __('messages.id') }}</th>
                        <th class="text-center">{{ __('messages.device_type') }}</th>
                        <th class="text-center">{{ __('messages.device_details') }}</th>
                        <th class="text-center">{{ __('messages.purchase_price_sar') }}</th>
                        <th class="text-center">{{ __('messages.currency') }}</th>
                        <th class="text-center">{{ __('messages.purchase_price_egp') }}</th>
                        <th class="text-center">{{ __('messages.extra_expenses') }}</th>
                        <th class="text-center">{{ __('messages.total_purchase_with_expenses') }}</th>
                        <th class="text-center">{{ __('messages.sale_price_egp') }}</th>
                        <th class="text-center">{{ __('messages.status') }}</th>
                        <th class="text-center">{{ __('messages.total_cost') }}</th>
                        <th class="text-center">{{ __('messages.net_profit_after_sale') }}</th>
                        <th class="text-center">{{ __('messages.created_by') }}</th>
                        @canany(['iphones_destroy','iphones_update','iphones_logs'])
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        @endcan
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    @include('dashboard.iphones.create')
    @include('dashboard.iphones.edit')
    @include('dashboard.iphones.delete')
    @include('dashboard.iphones.log-action')
    @include('dashboard.iphones.logs')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            loadIphones();

            $('#searchInput').on('keyup', function() {
                loadIphones();
            });

            function valueOrEmpty(value) {
                return value ?? '';
            }

            function escapeHtml(value) {
                return String(valueOrEmpty(value)).replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function escapeAttr(value) {
                return escapeHtml(value);
            }

            function actionTypeLabel(type) {
                const labels = {
                    sale: '{{ __('messages.iphone_action_sale') }}',
                    return: '{{ __('messages.iphone_action_return') }}',
                    maintenance: '{{ __('messages.iphone_action_maintenance') }}',
                    expense: '{{ __('messages.iphone_action_expense') }}'
                };

                return labels[type] || type;
            }

            function statusLabel(status) {
                const labels = {
                    available: '{{ __('messages.iphone_status_available') }}',
                    sold: '{{ __('messages.iphone_status_sold') }}',
                    returned: '{{ __('messages.iphone_status_returned') }}',
                    maintenance: '{{ __('messages.iphone_status_maintenance') }}'
                };

                return labels[status] || status;
            }

            function calculateModalTotals(prefix) {
                let purchaseEgp = parseFloat($(`#${prefix}PurchasePriceEgp`).val()) || 0;
                let extraExpenses = parseFloat($(`#${prefix}ExtraExpenses`).val()) || 0;
                let salePrice = $(`#${prefix}SalePriceEgp`).val();
                let total = purchaseEgp + extraExpenses;

                $(`#${prefix}TotalPurchase`).val(total.toFixed(2));
                $(`#${prefix}NetProfit`).val(salePrice !== '' ? (parseFloat(salePrice) - total).toFixed(2) : '');
            }

            $('.iphone-calculation').on('input', function() {
                calculateModalTotals($(this).data('prefix'));
            });

            function loadIphones() {
                $.get("{{ route('iphones.list') }}", {
                    search: $('#searchInput').val()
                }, function(res) {
                    if (!res.status) {
                        return;
                    }

                    let rows = '';
                    res.data.forEach((iphone, i) => {
                        rows += `
                            <tr>
                                <td data-label="{{ __('messages.id') }}">${i + 1}</td>
                                <td class="mobile-primary" data-label="{{ __('messages.device_type') }}">${escapeHtml(iphone.device_type)}</td>
                                <td class="mobile-muted" data-label="{{ __('messages.device_details') }}">${escapeHtml(iphone.device_details)}</td>
                                <td class="mobile-muted mobile-hide" data-label="{{ __('messages.purchase_price_sar') }}">${escapeHtml(iphone.purchase_price_sar)}</td>
                                <td class="mobile-muted mobile-hide" data-label="{{ __('messages.currency') }}">${escapeHtml(iphone.currency)}</td>
                                <td data-label="{{ __('messages.purchase_price_egp') }}">${escapeHtml(iphone.purchase_price_egp)}</td>
                                <td data-label="{{ __('messages.extra_expenses') }}">${escapeHtml(iphone.extra_expenses)}</td>
                                <td data-label="{{ __('messages.total_purchase_with_expenses') }}">${escapeHtml(iphone.total_purchase_with_expenses)}</td>
                                <td data-label="{{ __('messages.sale_price_egp') }}">${escapeHtml(iphone.sale_price_egp)}</td>
                                <td data-label="{{ __('messages.status') }}">${escapeHtml(statusLabel(iphone.status))}</td>
                                <td data-label="{{ __('messages.total_cost') }}">${escapeHtml(iphone.financial_summary?.total_cost)}</td>
                                <td data-label="{{ __('messages.net_profit_after_sale') }}">${escapeHtml(iphone.financial_summary?.net_profit)}</td>
                                <td class="mobile-muted mobile-hide" data-label="{{ __('messages.created_by') }}">${iphone.creator ? escapeHtml(iphone.creator.name) : ''}</td>
                                @canany(['iphones_destroy','iphones_update','iphones_logs'])
                                    <td class="mobile-actions" data-label="{{ __('messages.actions') }}">
                                        @can('iphones_logs')
                                            <button class="btn btn-outline-success btn-sm radius-8 addLogBtn"
                                                data-id="${iphone.id}"
                                                data-name="${escapeAttr(iphone.device_type)}"
                                                data-sale_client_id="${escapeAttr(iphone.sale_client?.id)}"
                                                data-sale_client_name="${escapeAttr(iphone.sale_client?.name)}">{{ __('messages.add_iphone_log') }}</button>
                                            <button class="btn btn-outline-info btn-sm radius-8 viewLogsBtn"
                                                data-id="${iphone.id}"
                                                data-name="${escapeAttr(iphone.device_type)}">{{ __('messages.logs') }}</button>
                                        @endcan
                                        @can('iphones_update')
                                            <button class="btn btn-outline-primary btn-sm radius-8 editBtn"
                                                data-id="${iphone.id}"
                                                data-device_type="${escapeAttr(iphone.device_type)}"
                                                data-device_details="${escapeAttr(iphone.device_details)}"
                                                data-purchase_price_sar="${escapeAttr(iphone.purchase_price_sar)}"
                                                data-currency="${escapeAttr(iphone.currency)}"
                                                data-purchase_price_egp="${escapeAttr(iphone.purchase_price_egp)}"
                                                data-extra_expenses="${escapeAttr(iphone.extra_expenses)}"
                                                data-sale_price_egp="${escapeAttr(iphone.sale_price_egp)}">{{ __('messages.edit') }}</button>
                                        @endcan
                                        @can('iphones_destroy')
                                            <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${iphone.id}" data-name="${escapeAttr(iphone.device_type)}">{{ __('messages.delete') }}</button>
                                        @endcan
                                    </td>
                                @endcan
                            </tr>`;
                    });

                    $('#iphonesTable tbody').html(rows);
                });
            }

            $('#createForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('iphones.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#createModal').modal('hide');
                        loadIphones();
                        showToast(res.message, 'success');
                        $('#createForm')[0].reset();
                        calculateModalTotals('create');
                    },
                    error: function(xhr) {
                        $('#createModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || '{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                $('#editId').val($(this).data('id'));
                $('#editDeviceType').val($(this).data('device_type'));
                $('#editDeviceDetails').val($(this).data('device_details'));
                $('#editPurchasePriceSar').val($(this).data('purchase_price_sar'));
                $('#editCurrency').val($(this).data('currency'));
                $('#editPurchasePriceEgp').val($(this).data('purchase_price_egp'));
                $('#editExtraExpenses').val($(this).data('extra_expenses'));
                $('#editSalePriceEgp').val($(this).data('sale_price_egp'));
                calculateModalTotals('edit');
                $('#editModal').modal('show');
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#editId').val();
                let data = $(this).serialize() + '&_method=PUT';

                $.ajax({
                    url: "/dashboard/iphones/" + id,
                    type: "POST",
                    data: data,
                    success: function(res) {
                        $('#editModal').modal('hide');
                        loadIphones();
                        showToast(res.message, 'success');
                    },
                    error: function(xhr) {
                        $('#editModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || '{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                $('#deleteId').val($(this).data('id'));
                $('#deleteName').text($(this).data('name'));
                $('#deleteModal').modal('show');
            });

            $(document).on('click', '.addLogBtn', function() {
                const saleClientId = $(this).data('sale_client_id');
                const saleClientName = $(this).data('sale_client_name');

                $('#logIphoneId').val($(this).data('id'));
                $('#logIphoneName').text($(this).data('name'));
                $('#logActionForm')[0].reset();
                updateLogClientLock(saleClientId, saleClientName);

                $('#logActionType').off('change.iphoneClientLock').on('change.iphoneClientLock', function() {
                    updateLogClientLock(saleClientId, saleClientName);
                });

                $('#logActionModal').modal('show');
            });

            function updateLogClientLock(saleClientId, saleClientName) {
                $('#lockedLogClientId').removeAttr('name').val('');
                $('#logClientId option').prop('disabled', false);
                $('#logClientId').prop('disabled', false).val('');
                $('#logClientLabelHint').text('({{ __('messages.optional') }})');
                $('#logClientHelperText').text('{{ __('messages.iphone_client_optional_helper') }}');

                if (!saleClientId || $('#logActionType').val() === 'sale') {
                    return;
                }

                $('#logClientId option').each(function() {
                    const optionValue = String($(this).val());

                    if (optionValue && optionValue !== String(saleClientId)) {
                        $(this).prop('disabled', true);
                    }
                });

                if ($('#logActionType').val() !== 'return') {
                    $('#logClientLabelHint').text('({{ __('messages.optional_same_sale_client') }})');
                    $('#logClientHelperText').text('{{ __('messages.iphone_client_internal_or_sale_client_helper') }}');
                    return;
                }

                $('#logClientId').val(saleClientId).prop('disabled', true);
                $('#lockedLogClientId').attr('name', 'client_id').val(saleClientId);
                $('#logClientLabelHint').text('({{ __('messages.required') }})');
                $('#logClientHelperText').text('{{ __('messages.iphone_client_return_helper') }}');
            }

            $('#logActionForm').submit(function(e) {
                e.preventDefault();
                let id = $('#logIphoneId').val();

                $.ajax({
                    url: "/dashboard/iphones/" + id + "/logs",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#logActionModal').modal('hide');
                        loadIphones();
                        showToast(res.message, 'success');
                    },
                    error: function(xhr) {
                        $('#logActionModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || '{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            });

            $(document).on('click', '.viewLogsBtn', function() {
                let id = $(this).data('id');
                $('#logsIphoneName').text($(this).data('name'));
                $('#iphoneLogsContent').html('<div class="text-center py-3">{{ __('messages.loading_text') }}</div>');
                $('#iphoneLogsModal').modal('show');

                $.get("/dashboard/iphones/" + id + "/logs", function(res) {
                    if (!res.status || !res.data.length) {
                        $('#iphoneLogsContent').html('<div class="text-center py-3">{{ __('messages.no_logs_found') }}</div>');
                        return;
                    }

                    let rows = '';
                    res.data.forEach((log, i) => {
                        rows += `
                            <tr>
                                <td data-label="{{ __('messages.id') }}">${i + 1}</td>
                                <td class="mobile-primary" data-label="{{ __('messages.action_type') }}">${escapeHtml(actionTypeLabel(log.action_type))}</td>
                                <td data-label="{{ __('messages.amount') }}">${escapeHtml(log.amount)}</td>
                                <td data-label="{{ __('messages.payment_way') }}">${log.payment_way ? escapeHtml(log.payment_way.name) : ''}</td>
                                <td data-label="{{ __('messages.client') }}">${log.client ? escapeHtml(log.client.name) : ''}</td>
                                <td data-label="{{ __('messages.transaction') }}">${log.transaction_id ? '#' + escapeHtml(log.transaction_id) : ''}</td>
                                <td class="mobile-muted" data-label="{{ __('messages.notes') }}">${escapeHtml(log.notes)}</td>
                                <td class="mobile-muted" data-label="{{ __('messages.created_by') }}">${log.creator ? escapeHtml(log.creator.name) : ''}</td>
                                <td class="mobile-muted" data-label="{{ __('messages.created_at') }}">${escapeHtml(log.created_at)}</td>
                            </tr>`;
                    });

                    $('#iphoneLogsContent').html(`
                        <div class="responsive-records-wrapper table-responsive">
                            <table class="text-center table table-bordered table-sm mb-0 responsive-records">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.id') }}</th>
                                        <th>{{ __('messages.action_type') }}</th>
                                        <th>{{ __('messages.amount') }}</th>
                                        <th>{{ __('messages.payment_way') }}</th>
                                        <th>{{ __('messages.client') }}</th>
                                        <th>{{ __('messages.transaction') }}</th>
                                        <th>{{ __('messages.notes') }}</th>
                                        <th>{{ __('messages.created_by') }}</th>
                                        <th>{{ __('messages.created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>`);
                });
            });

            $('#deleteForm').submit(function(e) {
                e.preventDefault();
                let id = $('#deleteId').val();

                $.ajax({
                    url: "/dashboard/iphones/" + id,
                    type: "DELETE",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        loadIphones();
                        showToast(res.message, 'success');
                    },
                    error: function(xhr) {
                        $('#deleteModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || '{{ __('messages.something_went_wrong') }}', 'error');
                    }
                });
            });
        });
    </script>
@endpush
