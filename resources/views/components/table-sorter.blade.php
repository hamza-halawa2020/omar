@once
    <style>
        table.js-sortable-table thead th[data-sortable-column] {
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            position: relative;
            padding-inline-end: 1.5rem;
        }

        table.js-sortable-table thead th[data-sortable-column]::after {
            content: "\f0dc";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            opacity: 0.35;
            font-size: 0.75rem;
            position: absolute;
            inset-inline-end: 0.45rem;
            top: 50%;
            transform: translateY(-50%);
        }

        table.js-sortable-table thead th[data-sort-direction="asc"]::after {
            content: "\f0de";
            opacity: 0.9;
        }

        table.js-sortable-table thead th[data-sort-direction="desc"]::after {
            content: "\f0dd";
            opacity: 0.9;
        }
    </style>

    <script>
        (function () {
            function normalizeText(value) {
                return String(value || '')
                    .replace(/\s+/g, ' ')
                    .replace(/\u00a0/g, ' ')
                    .trim();
            }

            function cellSortValue(cell) {
                if (!cell) {
                    return '';
                }

                return normalizeText(cell.getAttribute('data-sort-value') || cell.textContent);
            }

            function parseNumber(value) {
                const normalized = normalizeText(value)
                    .replace(/[,\s]/g, '')
                    .replace(/[^\d.\-]/g, '');

                if (!normalized || normalized === '-' || normalized === '.') {
                    return null;
                }

                const number = Number(normalized);
                return Number.isFinite(number) ? number : null;
            }

            function parseDate(value) {
                const text = normalizeText(value);
                if (!text) {
                    return null;
                }

                const timestamp = Date.parse(text.replace(' ', 'T'));
                return Number.isFinite(timestamp) ? timestamp : null;
            }

            function detectType(values) {
                const meaningful = values.filter(Boolean).slice(0, 12);
                if (!meaningful.length) {
                    return 'text';
                }

                if (meaningful.every(value => parseNumber(value) !== null)) {
                    return 'number';
                }

                if (meaningful.every(value => parseDate(value) !== null)) {
                    return 'date';
                }

                return 'text';
            }

            function compareValues(a, b, type, direction) {
                let result = 0;

                if (type === 'number') {
                    result = parseNumber(a) - parseNumber(b);
                } else if (type === 'date') {
                    result = parseDate(a) - parseDate(b);
                } else {
                    result = normalizeText(a).localeCompare(normalizeText(b), ['ar', 'en'], {
                        numeric: true,
                        sensitivity: 'base'
                    });
                }

                return direction === 'asc' ? result : -result;
            }

            function prepareTable(table) {
                if (!table || table.dataset.sortable === 'false') {
                    return;
                }

                const headers = table.querySelectorAll('thead th');
                if (!headers.length || !table.tBodies.length) {
                    return;
                }

                table.classList.add('js-sortable-table');

                headers.forEach((header, index) => {
                    if (header.dataset.sortable === 'false') {
                        return;
                    }

                    const label = normalizeText(header.textContent).toLowerCase();
                    if (!label || ['actions', 'action', 'إجراءات', 'الاجراءات', 'الإجراءات'].includes(label)) {
                        return;
                    }

                    if (table.dataset.backendSort === 'true' && !header.dataset.sortKey) {
                        return;
                    }

                    header.dataset.sortableColumn = String(index);
                    header.setAttribute('role', 'button');
                    header.setAttribute('tabindex', '0');
                });
            }

            function updateSortHeader(header, direction) {
                const table = header.closest('table');

                table.querySelectorAll('thead th[data-sort-direction]').forEach(th => {
                    if (th !== header) {
                        th.removeAttribute('data-sort-direction');
                    }
                });

                header.dataset.sortDirection = direction;
            }

            function sortTableByHeader(header) {
                const table = header.closest('table');
                const tbody = table && table.tBodies[0];
                const columnIndex = Number(header.dataset.sortableColumn);

                if (!table || !tbody || !Number.isInteger(columnIndex)) {
                    return;
                }

                const rows = Array.from(tbody.querySelectorAll('tr'))
                    .filter(row => row.children.length > columnIndex && !row.querySelector('td[colspan]'));

                if (rows.length < 2) {
                    return;
                }

                const currentDirection = header.dataset.sortDirection || 'desc';
                const nextDirection = currentDirection === 'asc' ? 'desc' : 'asc';

                if (table.dataset.backendSort === 'true') {
                    updateSortHeader(header, nextDirection);
                    const event = new CustomEvent('table:sort', {
                        bubbles: true,
                        cancelable: true,
                        detail: {
                            table,
                            sortBy: header.dataset.sortKey,
                            sortDirection: nextDirection,
                            columnIndex
                        }
                    });

                    table.dispatchEvent(event);

                    if (!event.defaultPrevented) {
                        const prefix = table.dataset.sortParamPrefix || '';
                        const sortByParam = `${prefix}sort_by`;
                        const sortDirectionParam = `${prefix}sort_direction`;
                        const pageParam = table.dataset.sortPageParam || 'page';
                        const url = new URL(window.location.href);

                        url.searchParams.set(sortByParam, header.dataset.sortKey);
                        url.searchParams.set(sortDirectionParam, nextDirection);
                        url.searchParams.delete(pageParam);

                        if (table.id) {
                            url.hash = table.id;
                        }

                        window.location.href = url.toString();
                    }

                    return;
                }

                const values = rows.map(row => cellSortValue(row.children[columnIndex]));
                const type = header.dataset.sortType || detectType(values);

                rows
                    .map((row, originalIndex) => ({
                        row,
                        originalIndex,
                        value: cellSortValue(row.children[columnIndex])
                    }))
                    .sort((left, right) => {
                        const compared = compareValues(left.value, right.value, type, nextDirection);
                        return compared || (left.originalIndex - right.originalIndex);
                    })
                    .forEach(item => tbody.appendChild(item.row));

                updateSortHeader(header, nextDirection);
            }

            function prepareAllTables(scope) {
                (scope || document).querySelectorAll('table').forEach(prepareTable);
            }

            document.addEventListener('DOMContentLoaded', () => {
                prepareAllTables(document);

                const observer = new MutationObserver(mutations => {
                    mutations.forEach(mutation => {
                        mutation.addedNodes.forEach(node => {
                            if (!(node instanceof HTMLElement)) {
                                return;
                            }

                            if (node.matches('table')) {
                                prepareTable(node);
                            } else if (node.querySelectorAll) {
                                prepareAllTables(node);
                            }
                        });
                    });
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            });

            document.addEventListener('click', event => {
                const header = event.target.closest('th[data-sortable-column]');
                if (!header) {
                    return;
                }

                prepareTable(header.closest('table'));
                sortTableByHeader(header);
            });

            document.addEventListener('keydown', event => {
                if (!['Enter', ' '].includes(event.key)) {
                    return;
                }

                const header = event.target.closest('th[data-sortable-column]');
                if (!header) {
                    return;
                }

                event.preventDefault();
                prepareTable(header.closest('table'));
                sortTableByHeader(header);
            });

            window.refreshSortableTables = function (scope) {
                prepareAllTables(scope || document);
            };
        })();
    </script>
@endonce
