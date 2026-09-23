@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <style>
        .products-page .table-pager {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            direction: ltr;
            white-space: nowrap;
        }

        .products-page .table-pager .btn {
            min-width: 36px;
            height: 36px;
            padding: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .products-page .table-pager .page-status {
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
            .products-page .table-pager {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="container products-page">
        <div class="d-flex justify-content-between mb-3 mobile-stack-header">
            <div class="fw-bold fs-5">{{ __('messages.products') }}</div>
            @can('products_store')
                @can('purchase_prices_view')
                    <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal"
                        data-bs-target="#createModal">{{ __('messages.add_product') }}</button>
                @endcan
            @endcan
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>{{ __('messages.search') }}</label>
                        <input type="text" id="searchInput" class="form-control mt-1" placeholder="{{ __('messages.search') }}">
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('messages.code') }}</label>
                        <select id="codeFilter" class="form-control mt-1">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach ($productCodes as $productCode)
                                <option value="{{ $productCode }}">{{ $productCode }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="responsive-records-wrapper table-responsive">
            <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records" id="productsTable">
                <thead>
                    <tr>
                        <th class="text-center">{{ __('messages.id') }}</th>
                        <th class="text-center">{{ __('messages.name') }}</th>
                        <th class="text-center">{{ __('messages.code') }}</th>
                        <th class="text-center">{{ __('messages.image') }}</th>
                        <th class="text-center">{{ __('messages.description') }}</th>
                        @can('purchase_prices_view')
                            <th class="text-center">{{ __('messages.purchase_price') }}</th>
                        @endcan
                        <th class="text-center">{{ __('messages.sale_price') }}</th>
                        <th class="text-center">{{ __('messages.stock') }}</th>
                        <th class="text-center">{{ __('messages.created_by') }}</th>
                        @canany(['products_destroy','products_update','products_show'])
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
            <small class="text-muted" id="productsPaginationInfo"></small>
            <div class="table-pager" id="productsPagination"></div>
        </div>
    </div>

    <!-- Create Modal -->
    @include('dashboard.products.create')
    <!-- Edit Modal -->
    @include('dashboard.products.edit')
    <!-- Delete Modal -->
    @include('dashboard.products.delete')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const canViewPurchasePrices = @can('purchase_prices_view') true @else false @endcan;
            let currentPage = 1;
            const perPage = 25;

            loadproducts();

            $('#searchInput').on('keyup', function() {
                currentPage = 1;
                loadproducts();
            });

            $('#codeFilter').on('change', function() {
                currentPage = 1;
                loadproducts();
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

            function loadproducts(page = currentPage) {
                currentPage = page;
                let search = $('#searchInput').val();
                let code = $('#codeFilter').val();

                $.get("{{ route('products.list') }}", {
                    search: search,
                    code: code,
                    page: currentPage,
                    per_page: perPage
                }, function(res) {
                    if (res.status) {
                        refreshCodeFilter(res.codes || []);

                        let rows = '';
                        let parentOptions = '<option value="">{{ __('messages.none') }}</option>';
                        res.data.forEach((cat, i) => {
                            let imageHtml = cat.image ? `<img src="/${escapeHtml(cat.image)}" width="50" class="rounded">` : '';
                            rows += `
                <tr>
                    <td data-label="{{ __('messages.id') }}">${i+1}</td>
                    <td class="mobile-primary" data-label="{{ __('messages.name') }}">${escapeHtml(cat.name)}</td>
                    <td data-label="{{ __('messages.code') }}">${escapeHtml(cat.code)}</td>
                    <td class="mobile-muted mobile-hide" data-label="{{ __('messages.image') }}">${imageHtml}</td>
                    <td class="mobile-muted" data-label="{{ __('messages.description') }}">${escapeHtml(cat.description)}</td>
                    ${canViewPurchasePrices ? `
                        <td data-label="{{ __('messages.purchase_price') }}">${escapeHtml(cat.purchase_price)}</td>
                    ` : ''}
                    <td data-label="{{ __('messages.sale_price') }}">${escapeHtml(cat.sale_price)}</td>
                    <td data-label="{{ __('messages.stock') }}">${escapeHtml(cat.stock)}</td>
                    <td class="mobile-muted mobile-hide" data-label="{{ __('messages.created_by') }}">${cat.creator ? escapeHtml(cat.creator.name) : ''}</td>
                    @canany(['products_destroy','products_update','products_show'])
                        <td class="mobile-actions" data-label="{{ __('messages.actions') }}">
                            @can('products_show')
                                <a href="/dashboard/products/${cat.id}/details" class="btn btn-outline-success btn-sm radius-8">{{ __('messages.details') }}</a>
                            @endcan
                            @can('products_update')
                                <button class="btn btn-outline-primary btn-sm radius-8 editBtn" 
                                data-id="${cat.id}" 
                                data-name="${escapeHtml(cat.name)}" 
                                data-code="${escapeHtml(cat.code)}" 
                                data-description="${escapeHtml(cat.description)}" 
                                ${canViewPurchasePrices ? `data-purchase_price="${escapeHtml(cat.purchase_price)}"` : ''}
                                data-sale_price="${escapeHtml(cat.sale_price)}" 
                                data-stock="${escapeHtml(cat.stock)}">{{ __('messages.edit') }}</button>
                            @endcan
                            @can('products_destroy')
                                <button class="btn btn-outline-danger btn-sm radius-8 deleteBtn" data-id="${cat.id}" data-name="${escapeHtml(cat.name)}">{{ __('messages.delete') }}</button>
                            @endcan
                        </td>
                    @endcan
                </tr>`;
                            parentOptions += `<option value="${cat.id}">${cat.name}</option>`;
                        });
                        $('#productsTable tbody').html(rows);
                        $('#parentSelect').html(parentOptions);
                        $('#editParent').html(parentOptions);
                        renderProductsPagination(res.meta || {});
                    }
                });
            }

            function renderProductsPagination(meta) {
                let from = meta.from || 0;
                let to = meta.to || 0;
                let total = meta.total || 0;
                let lastPage = meta.last_page || 1;
                let page = meta.current_page || 1;

                $('#productsPaginationInfo').text(`${from} - ${to} / ${total}`);

                if (lastPage <= 1) {
                    $('#productsPagination').empty();
                    return;
                }

                $('#productsPagination').html(`
                    <button type="button" class="btn btn-outline-primary ${page <= 1 ? 'disabled' : ''}" data-page="${page - 1}">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="page-status">${page} / ${lastPage}</span>
                    <button type="button" class="btn btn-outline-primary ${page >= lastPage ? 'disabled' : ''}" data-page="${page + 1}">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `);
            }

            $(document).on('click', '#productsPagination button:not(.disabled)', function() {
                let page = Number($(this).data('page'));
                if (!page) {
                    return;
                }

                loadproducts(page);
            });

            function refreshCodeFilter(codes) {
                let selectedCode = $('#codeFilter').val();
                let codeFilter = $('#codeFilter');

                codeFilter.empty().append(
                    $('<option>', {
                        value: '',
                        text: '{{ __('messages.all') }}'
                    })
                );

                codes.forEach(function(item) {
                    let code = item.code ?? item;
                    codeFilter.append(
                        $('<option>', {
                            value: code,
                            text: code,
                            selected: code == selectedCode
                        })
                    );
                });
            }

            // Create
            $('#createForm').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('products.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            $('#createModal').modal('hide');
                            loadproducts();
                            showToast(res.message, 'success');
                            $('#createForm')[0].reset();
                        } else {
                            $('#createModal').modal('hide');
                            showToast(res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#createModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || 'Something went wrong', 'error');
                    }
                });
            });


            // Edit (open modal)
            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let code = $(this).data('code');
                let image = $(this).data('image');
                let description = $(this).data('description');
                let purchase_price = $(this).data('purchase_price');
                let sale_price = $(this).data('sale_price');
                let stock = $(this).data('stock');


                $('#editId').val(id);
                $('#editName').val(name);
                $('#editCode').val(code);
                $('#editImage').val(image);
                $('#editDescription').val(description);
                if (canViewPurchasePrices) {
                    $('#editPurchasePrice').val(purchase_price);
                }
                $('#editSalePrice').val(sale_price);
                $('#editStock').val(stock);

                $.get("{{ route('products.list') }}", function(res) {
                    if (res.status) {
                        $('#editModal').modal('show');
                    }
                });
            });



            // Update
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#editId').val();
                let formData = new FormData(this);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: "/dashboard/products/" + id,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            $('#editModal').modal('hide');
                            loadproducts();
                            showToast(res.message, 'success');
                        } else {
                            $('#editModal').modal('hide');
                            showToast(res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#editModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || 'Something went wrong', 'error');
                    }
                });
            });

            // Delete (open modal)
            $(document).on('click', '.deleteBtn', function() {
                $('#deleteId').val($(this).data('id'));
                $('#deleteName').text($(this).data('name'));
                $('#deleteModal').modal('show');
            });

            // Confirm Delete
            $('#deleteForm').submit(function(e) {
                e.preventDefault();
                let id = $('#deleteId').val();
                $.ajax({
                    url: "/dashboard/products/" + id,
                    type: "DELETE",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            $('#deleteModal').modal('hide');
                            loadproducts();
                            showToast(res.message, 'success');
                        } else {
                            $('#deleteModal').modal('hide');
                            showToast(res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $('#deleteModal').modal('hide');
                        let res = xhr.responseJSON;
                        showToast(res?.message || 'Something went wrong', 'error');
                    }
                });
            });

        });
    </script>
@endpush
