@extends('dashboard.layouts.app')

@section('content')
@include('components.alert')

<style>
    .associations-page .table-pager {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        direction: ltr;
        white-space: nowrap;
    }

    .associations-page .table-pager .btn {
        min-width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .associations-page .table-pager .page-status {
        min-width: 92px;
        height: 36px;
        padding: 0 12px;
        border: 1px solid #d8dee8;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        color: #475569;
        font-size: 13px;
    }

    @media (max-width: 767.98px) {
        .associations-page .table-pager {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="container associations-page">
    <div class="d-flex justify-content-between mb-3 mobile-stack-header">
        <div class="fw-bold fs-5">{{ __('messages.associations') }}</div>

        @can('associations_store')
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal"
                data-bs-target="#createModal">{{ __('messages.add_association') }}</button>
        @endcan
    </div>

    <div class="responsive-records-wrapper table-responsive">
    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records" id="associationsTable">
        <thead>
            <tr>
                <th class="text-center">{{ __('messages.id') }}</th>
                <th class="text-center">{{ __('messages.name') }}</th>
                <th class="text-center">{{ __('messages.per_day') }}</th>
                <th class="text-center">{{ __('messages.total_members') }}</th>
                <th class="text-center">{{ __('messages.monthly_amount') }}</th>
                <th class="text-center">{{ __('messages.status') }}</th>
                <th class="text-center">{{ __('messages.created_by') }}</th>
                @canany(['associations_update', 'associations_destroy', 'associations_details'])
                <th class="text-center">{{ __('messages.actions') }}</th>
                @endcan
            </tr>
        </thead>
        <tbody>
            {{-- Loaded by AJAX --}}
        </tbody>
    </table>
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
        <small class="text-muted" id="associationsPaginationInfo"></small>
        <div class="table-pager" id="associationsPagination"></div>
    </div>
</div>


<!-- Create Modal -->
@include('dashboard.associations.create')
<!-- Edit Modal -->
@include('dashboard.associations.edit')
<!-- Delete Modal -->
@include('dashboard.associations.delete')
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        let currentPage = 1;
        const perPage = 25;

        loadAssociations();

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

        function loadAssociations(page = currentPage) {
            currentPage = page;

            $.get("{{ route('associations.list') }}", {
                page: currentPage,
                per_page: perPage
            }, function (res) {
                if (res.status) {
                    let rows = '';
                    let rowStart = res.meta?.from || 1;
                    res.data.forEach((assoc, i) => {
                        rows += `
                                <tr>
                                    <td data-label="{{ __('messages.id') }}">${rowStart + i}</td>
                                    <td class="mobile-primary" data-label="{{ __('messages.name') }}">${escapeHtml(assoc.name)}</td>
                                    <td data-label="{{ __('messages.per_day') }}">${escapeHtml(assoc.per_day)}</td>
                                    <td data-label="{{ __('messages.total_members') }}">${escapeHtml(assoc.total_members)}</td>
                                    <td data-label="{{ __('messages.monthly_amount') }}">${escapeHtml(assoc.monthly_amount)}</td>
                                    <td data-label="{{ __('messages.status') }}">${escapeHtml(assoc.status)}</td>
                                    <td class="mobile-muted mobile-hide" data-label="{{ __('messages.created_by') }}">${assoc.creator ? escapeHtml(assoc.creator.name) : ''}</td>
                                    @canany(['associations_update', 'associations_destroy', 'associations_details'])
                                        <td class="mobile-actions" data-label="{{ __('messages.actions') }}">
                                            @can('associations_details')
                                                <a href="/dashboard/associations/${assoc.id}/details" 
                                                class="btn btn-outline-primary btn-sm radius-8">
                                                    {{ __('messages.details') }}
                                                </a>
                                            @endcan
                                            @can('associations_update')
                                                <button class="btn btn-outline-primary btn-sm radius-8 editBtn"
                                                    data-id="${assoc.id}"
                                                    data-name="${escapeHtml(assoc.name)}"
                                                    data-per_day="${escapeHtml(assoc.per_day)}"
                                                    data-total="${escapeHtml(assoc.total_members)}"
                                                    data-amount="${escapeHtml(assoc.monthly_amount)}"
                                                    data-status="${escapeHtml(assoc.status)}"
                                                    data-start="${escapeHtml(assoc.start_date)}"
                                                    data-end="${escapeHtml(assoc.end_date)}">
                                                    {{ __('messages.edit') }}
                                                </button>
                                            @endcan
                                            @can('associations_destroy')
                                                <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn"
                                                    data-id="${assoc.id}" data-name="${escapeHtml(assoc.name)}">
                                                    {{ __('messages.delete') }}
                                                </button>
                                            @endcan
                                        </td>
                                    @endcan
                                </tr>`;
                    });
                    $('#associationsTable tbody').html(rows);
                    renderAssociationsPagination(res.meta || {});
                }
            });
        }

        function renderAssociationsPagination(meta) {
            let from = meta.from || 0;
            let to = meta.to || 0;
            let total = meta.total || 0;
            let lastPage = meta.last_page || 1;
            let page = meta.current_page || 1;

            $('#associationsPaginationInfo').text(`${from} - ${to} / ${total}`);

            if (lastPage <= 1) {
                $('#associationsPagination').empty();
                return;
            }

            $('#associationsPagination').html(`
                <button type="button" class="btn btn-outline-primary ${page <= 1 ? 'disabled' : ''}" data-page="${page - 1}">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="page-status">${page} / ${lastPage}</span>
                <button type="button" class="btn btn-outline-primary ${page >= lastPage ? 'disabled' : ''}" data-page="${page + 1}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `);
        }

        $(document).on('click', '#associationsPagination button:not(.disabled)', function () {
            let page = Number($(this).data('page'));
            if (!page) {
                return;
            }

            loadAssociations(page);
        });

        // Create
        $('#createForm').submit(function (e) {
            e.preventDefault();
            $.post("{{ route('associations.store') }}", $(this).serialize(), function (res) {
                if (res.status) {
                    $('#createModal').modal('hide');
                    loadAssociations();
                    showToast(res.message, 'success');
                    $('#createForm')[0].reset();
                } else {
                    $('#createModal').modal('hide');
                    showToast(res.message, 'error');
                }
            });
        });

        // Edit (open modal)
        $(document).on('click', '.editBtn', function () {
            $('#editId').val($(this).data('id'));
            $('#editName').val($(this).data('name'));
            $('#editPerDay').val($(this).data('per_day'));
            $('#editTotalMembers').val($(this).data('total'));
            $('#editMonthlyAmount').val($(this).data('amount'));
            $('#editStatus').val($(this).data('status'));
            $('#editStartDate').val($(this).data('start'));
            $('#editEndDate').val($(this).data('end'));
            $('#editModal').modal('show');
        });

        // Update
        $('#editForm').submit(function (e) {
            e.preventDefault();
            let id = $('#editId').val();
            $.ajax({
                url: "/dashboard/associations/" + id,
                type: "PUT",
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status) {
                        $('#editModal').modal('hide');
                        loadAssociations();
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
                url: "/dashboard/associations/" + id,
                type: "DELETE",
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status) {
                        $('#deleteModal').modal('hide');
                        loadAssociations();
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
