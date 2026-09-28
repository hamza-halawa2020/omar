@props([
    'id',
    'paginator' => null,
    'pageName' => 'page',
    'perPageParam' => 'per_page',
    'fragment' => null,
    'perPage' => 25,
    'perPageOptions' => [10, 25, 50, 100],
])

@once
    <style>
        .table-pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin: 1rem 0;
        }

        .table-pagination__info {
            color: #64748b;
            font-size: 0.875rem;
        }

        .table-pagination__controls {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
            direction: ltr;
        }

        .table-pagination__button {
            min-width: 36px;
            height: 36px;
            padding: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .table-pagination__status,
        .table-pagination__select,
        .table-pagination__page-input {
            height: 36px;
            border: 1px solid #d8dee8;
            border-radius: 8px;
            background: #fff;
            color: #475569;
            font-size: 13px;
        }

        .table-pagination__status {
            min-width: 92px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .table-pagination__select {
            min-width: 84px;
            padding: 0 0.5rem;
        }

        .table-pagination__page-input {
            width: 76px;
            padding: 0 0.5rem;
            text-align: center;
        }

        @media (max-width: 767.98px) {
            .table-pagination {
                justify-content: center;
            }

            .table-pagination__info,
            .table-pagination__controls {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
        }
    </style>

    <script>
        window.TablePagination = window.TablePagination || (function () {
            function normalizeMeta(meta) {
                return {
                    from: Number(meta?.from || 0),
                    to: Number(meta?.to || 0),
                    total: Number(meta?.total || 0),
                    current_page: Number(meta?.current_page || 1),
                    last_page: Math.max(Number(meta?.last_page || 1), 1),
                    per_page: Number(meta?.per_page || 25)
                };
            }

            function optionsHtml(options, selected) {
                return options.map(function (option) {
                    const value = Number(option);
                    return `<option value="${value}" ${value === selected ? 'selected' : ''}>${value}</option>`;
                }).join('');
            }

            function render(target, meta, config = {}) {
                const element = typeof target === 'string' ? document.getElementById(target) : target;
                if (!element) return;

                const normalized = normalizeMeta(meta || {});
                const perPageOptions = (config.perPageOptions || element.dataset.perPageOptions || '10,25,50,100')
                    .toString()
                    .split(',')
                    .map(function (value) { return Number(value.trim()); })
                    .filter(Boolean);

                if (!perPageOptions.includes(normalized.per_page)) {
                    perPageOptions.push(normalized.per_page);
                    perPageOptions.sort(function (a, b) { return a - b; });
                }

                element.innerHTML = `
                    <small class="table-pagination__info">${normalized.from} - ${normalized.to} / ${normalized.total}</small>
                    <div class="table-pagination__controls">
                        <select class="table-pagination__select" data-pagination-per-page aria-label="Rows per page">
                            ${optionsHtml(perPageOptions, normalized.per_page)}
                        </select>
                        <button type="button" class="btn btn-outline-primary table-pagination__button ${normalized.current_page <= 1 ? 'disabled' : ''}" data-pagination-page="${normalized.current_page - 1}">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="table-pagination__status">${normalized.current_page} / ${normalized.last_page}</span>
                        <button type="button" class="btn btn-outline-primary table-pagination__button ${normalized.current_page >= normalized.last_page ? 'disabled' : ''}" data-pagination-page="${normalized.current_page + 1}">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <input type="number" min="1" max="${normalized.last_page}" value="${normalized.current_page}" class="table-pagination__page-input" data-pagination-jump aria-label="Go to page">
                    </div>
                `;

                element.dataset.currentPage = normalized.current_page;
                element.dataset.lastPage = normalized.last_page;
                element.dataset.perPage = normalized.per_page;
            }

            function dispatch(element, name, detail) {
                element.dispatchEvent(new CustomEvent(name, { bubbles: true, detail }));
            }

            document.addEventListener('click', function (event) {
                const button = event.target.closest('[data-pagination-page]');
                if (!button || button.classList.contains('disabled')) return;

                const wrapper = button.closest('[data-table-pagination]');
                if (!wrapper) return;

                dispatch(wrapper, 'table:page', { page: Number(button.dataset.paginationPage) });
            });

            document.addEventListener('change', function (event) {
                const select = event.target.closest('[data-pagination-per-page]');
                if (!select) return;

                const wrapper = select.closest('[data-table-pagination]');
                if (!wrapper) return;

                if (wrapper.dataset.serverPagination === 'true') {
                    const url = new URL(window.location.href);
                    url.searchParams.set(wrapper.dataset.perPageParam || 'per_page', select.value);
                    url.searchParams.delete(wrapper.dataset.pageName || 'page');
                    if (wrapper.dataset.fragment) {
                        url.hash = wrapper.dataset.fragment;
                    }
                    window.location.href = url.toString();
                    return;
                }

                dispatch(wrapper, 'table:per-page', { perPage: Number(select.value) });
            });

            document.addEventListener('keydown', function (event) {
                const input = event.target.closest('[data-pagination-jump]');
                if (!input || event.key !== 'Enter') return;

                const wrapper = input.closest('[data-table-pagination]');
                if (!wrapper) return;

                const lastPage = Number(wrapper.dataset.lastPage || 1);
                const page = Math.min(Math.max(Number(input.value || 1), 1), lastPage);
                input.value = page;

                if (wrapper.dataset.serverPagination === 'true') {
                    const url = new URL(window.location.href);
                    url.searchParams.set(wrapper.dataset.pageName || 'page', page);
                    if (wrapper.dataset.fragment) {
                        url.hash = wrapper.dataset.fragment;
                    }
                    window.location.href = url.toString();
                    return;
                }

                dispatch(wrapper, 'table:page', { page });
            });

            return { render };
        })();
    </script>
@endonce

@if ($paginator)
    @php
        $currentPerPage = (int) request($perPageParam, $paginator->perPage());
        $serverOptions = collect($perPageOptions)->map(fn ($option) => (int) $option)->push($currentPerPage)->unique()->sort()->values();
        $pageUrl = function (int $page) use ($paginator, $pageName, $fragment) {
            $url = $paginator->url($page);

            return $fragment ? $url.'#'.$fragment : $url;
        };
    @endphp

    <div
        id="{{ $id }}"
        class="table-pagination"
        data-table-pagination
        data-server-pagination="true"
        data-page-name="{{ $pageName }}"
        data-per-page-param="{{ $perPageParam }}"
        data-fragment="{{ $fragment }}"
        data-current-page="{{ $paginator->currentPage() }}"
        data-last-page="{{ $paginator->lastPage() }}"
        data-per-page="{{ $currentPerPage }}"
    >
        <small class="table-pagination__info">
            {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} / {{ $paginator->total() }}
        </small>
        <div class="table-pagination__controls">
            <select class="table-pagination__select" data-pagination-per-page aria-label="Rows per page">
                @foreach ($serverOptions as $option)
                    <option value="{{ $option }}" @selected($option === $currentPerPage)>{{ $option }}</option>
                @endforeach
            </select>
            <a class="btn btn-outline-primary table-pagination__button {{ $paginator->onFirstPage() ? 'disabled' : '' }}"
                href="{{ $paginator->onFirstPage() ? '#' : $pageUrl($paginator->currentPage() - 1) }}">
                <i class="fas fa-chevron-left"></i>
            </a>
            <span class="table-pagination__status">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            <a class="btn btn-outline-primary table-pagination__button {{ $paginator->hasMorePages() ? '' : 'disabled' }}"
                href="{{ $paginator->hasMorePages() ? $pageUrl($paginator->currentPage() + 1) : '#' }}">
                <i class="fas fa-chevron-right"></i>
            </a>
            <input type="number" min="1" max="{{ $paginator->lastPage() }}" value="{{ $paginator->currentPage() }}" class="table-pagination__page-input" data-pagination-jump aria-label="Go to page">
        </div>
    </div>
@else
    <div
        id="{{ $id }}"
        class="table-pagination"
        data-table-pagination
        data-per-page="{{ $perPage }}"
        data-per-page-options="{{ implode(',', $perPageOptions) }}"
    ></div>
@endif
