@extends('dashboard.layouts.app')

@section('content')
@include('components.alert')

<style>
    .clients-paginated-page .table-pager{display:inline-flex;align-items:center;gap:8px;direction:ltr;white-space:nowrap}
    .clients-paginated-page .table-pager .btn{min-width:36px;height:36px;padding:0;border-radius:8px;display:inline-flex;align-items:center;justify-content:center}
    .clients-paginated-page .table-pager .page-status{min-width:92px;height:36px;padding:0 12px;border:1px solid #d8dee8;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;background:#fff;color:#475569;font-size:13px}
    @media (max-width:767.98px){.clients-paginated-page .table-pager{width:100%;justify-content:center}}
</style>

<div class="container clients-paginated-page">
    <div class="d-flex justify-content-between mb-3 mobile-stack-header">
        <div class="fw-bold fs-5">{{ __('messages.debts') }}</div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control"
                placeholder="{{ __('messages.search_by_name_or_phone') }}">
        </div>
    </div>

    <div class="responsive-records-wrapper table-responsive">
    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records" id="clientsTable">
        <thead>
            <tr>
                <th class="text-center">{{ __('messages.id') }}</th>
                <th class="text-center">{{ __('messages.name') }}</th>
                <th class="text-center">{{ __('messages.phone_number') }}</th>
                <th class="text-center">{{ __('messages.debt') }}</th>
                <th class="text-center">{{ __('messages.created_by') }}</th>
                @canany(['clients_show', 'clients_update', 'clients_destroy'])
                <th class="text-center">{{ __('messages.actions') }}</th>
                @endcan
            </tr>
        </thead>
        <tbody>
            {{-- Data will be loaded via AJAX --}}
        </tbody>
    </table>
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
        <small class="text-muted" id="clientsPaginationInfo"></small>
        <div class="table-pager" id="clientsPagination"></div>
    </div>
</div>

<!-- Edit Modal -->
@include('dashboard.clients.edit')
<!-- Delete Modal -->
@include('dashboard.clients.delete')
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let currentPage = 1;
        const perPage = 25;

        loadclients();
        $('#searchInput').on('keyup', function () {
            currentPage = 1;
            loadclients();
        });

        function loadclients(page = currentPage) {
            currentPage = page;
            const search = $('#searchInput').val();

            $.get("{{ route('clients.listDebts') }}", { search, page: currentPage, per_page: perPage }, function (res) {

                if (res.status) {
                    let rows = '';
                    let rowStart = res.meta?.from || 1;
                    res.data.forEach((client, i) => {
                        rows += `
                            <tr>
                                <td data-label="{{ __('messages.id') }}">${rowStart + i}</td>
                                <td class="mobile-primary" data-label="{{ __('messages.name') }}">${client.name}</td>
                                <td data-label="{{ __('messages.phone_number') }}">${client.full_phone_number || client.phone_number || ''}</td>
                                <td data-label="{{ __('messages.debt') }}">${client.debt}</td>
                                <td class="mobile-muted mobile-hide" data-label="{{ __('messages.created_by') }}">${client.creator ? client.creator.name : ''}</td>
                                @canany(['clients_show', 'clients_update', 'clients_destroy'])   
                                    <td class="mobile-actions" data-label="{{ __('messages.actions') }}">
                                        @can('clients_show')   
                                            <a href="{{ url('dashboard/clients') }}/${client.id}" class="btn btn-outline-success btn-sm radius-8 btn-sm">{{ __('messages.details') }}</a>
                                        @endcan
                                        @can('clients_update')
                                            <button class="btn btn-outline-primary btn-sm radius-8 editBtn"data-id="${client.id}"data-name="${client.name}"data-country_code="${client.country_code || '+20'}"data-phone_number="${client.phone_number}"data-debt="${client.debt}">{{ __('messages.edit') }}</button>
                                        @endcan
                                        @can('clients_destroy')
                                            <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${client.id}" data-name="${client.name}">{{ __('messages.delete') }}</button>
                                        @endcan
                                    </td>
                                @endcan
                            </tr>`;
                    });
                    $('#clientsTable tbody').html(rows);
                    renderClientsPagination(res.meta || {});
                }
            });
        }

        function renderClientsPagination(meta) {
            let from = meta.from || 0;
            let to = meta.to || 0;
            let total = meta.total || 0;
            let lastPage = meta.last_page || 1;
            let page = meta.current_page || 1;

            $('#clientsPaginationInfo').text(`${from} - ${to} / ${total}`);

            if (lastPage <= 1) {
                $('#clientsPagination').empty();
                return;
            }

            $('#clientsPagination').html(`
                <button type="button" class="btn btn-outline-primary ${page <= 1 ? 'disabled' : ''}" data-page="${page - 1}">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="page-status">${page} / ${lastPage}</span>
                <button type="button" class="btn btn-outline-primary ${page >= lastPage ? 'disabled' : ''}" data-page="${page + 1}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `);
        }

        $(document).on('click', '#clientsPagination button:not(.disabled)', function () {
            let page = Number($(this).data('page'));
            if (!page) return;
            loadclients(page);
        });


        // Edit (open modal)
        $(document).on('click', '.editBtn', function () {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let phone_number = $(this).data('phone_number');
            let country_code = $(this).data('country_code');
            let debt = $(this).data('debt');

            $('#editId').val(id);
            $('#editName').val(name);
            $('#editCountryCode').val(country_code || '+20');
            $('#editPhoneNumber').val(phone_number);
            $('#editDebt').val(debt);

            $('#editModal').modal('show');

        });

        // Update
        $('#editForm').submit(function (e) {
            e.preventDefault();
            let id = $('#editId').val();
            $.ajax({
                url: "/dashboard/clients/" + id,
                type: "PUT",
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status) {
                        $('#editModal').modal('hide');
                        loadclients();
                        showToast(res.message, 'success');
                    } else {
                        $('#editModal').modal('hide');
                        showToast(res.message, 'error');
                    }
                },
                error: function (xhr) {
                    $('#editModal').modal('hide');
                    let res = xhr.responseJSON;
                    showToast(res?.message || 'Something went wrong', 'error');
                }
            });
        });

        // Delete (open modal)
        $(document).on('click', '.deleteBtn', function () {
            $('#deleteId').val($(this).data('id'));
            $('#deleteName').text($(this).data('name'));
            $('#deleteModal').modal('show');
        });

        // Confirm Delete
        $('#deleteForm').submit(function (e) {
            e.preventDefault();
            let id = $('#deleteId').val();
            $.ajax({
                url: "/dashboard/clients/" + id,
                type: "DELETE",
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status) {
                        $('#deleteModal').modal('hide');
                        loadclients();
                        showToast(res.message, 'success');
                    } else {
                        $('#deleteModal').modal('hide');
                        showToast(res.message, 'error');
                    }
                },
                error: function (xhr) {
                    $('#deleteModal').modal('hide');
                    let res = xhr.responseJSON;
                    showToast(res?.message || 'Something went wrong', 'error');
                }
            });
        });

    });
</script>
@endpush
