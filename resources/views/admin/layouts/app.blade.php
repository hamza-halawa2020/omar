<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.admin.admin_panel'))</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/1.png') }}" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link href="{{ asset('assets/css/lib/font-awesome/6.4.2.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        :root {
            --admin-bg: #f5f7fb;
            --admin-surface: #ffffff;
            --admin-border: #e6eaf2;
            --admin-text: #172033;
            --admin-muted: #667085;
            --admin-primary: #2563eb;
            --admin-primary-soft: #eaf1ff;
            --admin-success-soft: #e8f7ee;
            --admin-warning-soft: #fff6df;
        }

        body {
            background: var(--admin-bg);
            color: var(--admin-text);
            min-height: 100vh;
        }

        .admin-shell {
            min-height: 100vh;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--admin-text);
            text-decoration: none;
        }

        .admin-brand img {
            width: 30px;
            height: 30px;
            object-fit: contain;
            border-radius: 8px;
        }

        .admin-main {
            min-width: 0;
        }

        .admin-topbar {
            min-height: 62px;
            background: rgba(255,255,255,.92);
            border-bottom: 1px solid var(--admin-border);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .admin-content {
            max-width: 1320px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-page-title {
            font-size: 15px;
            font-weight: 800;
            margin: 0;
        }

        .admin-page-subtitle {
            color: var(--admin-muted);
            font-size: 13px;
            margin: 4px 0 0;
        }

        .admin-content h4.fw-bold,
        .admin-content h5.fw-bold {
            font-size: 18px;
        }

        .admin-panel,
        .metric-card {
            background: var(--admin-surface);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .metric-card {
            padding: 14px;
        }

        .metric-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--admin-primary-soft);
            color: var(--admin-primary);
            font-size: 18px;
        }

        .table.admin-table > :not(caption) > * > * {
            padding: 12px 14px;
            vertical-align: middle;
            border-color: var(--admin-border);
        }

        .admin-table thead th {
            color: var(--admin-muted);
            font-size: 13px;
            font-weight: 700;
            background: #f8fafc;
        }

        .btn {
            border-radius: 8px;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 13px;
        }

        .admin-icon-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .admin-action-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge {
            border-radius: 999px;
            padding: 5px 9px;
            font-weight: 700;
            font-size: 12px;
        }

        @media (max-width: 991.98px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-content {
                padding: 18px;
            }
        }
    </style>
</head>

<body>
    <div class="admin-shell">
        <main class="admin-main">
            <header class="admin-topbar d-flex align-items-center justify-content-between px-4">
                <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('assets/images/1.png') }}" alt="logo">
                    <span class="fw-bold small">{{ __('messages.admin.admin_panel_short') }}</span>
                </a>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        {{ __('messages.admin.home') }}
                    </a>
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm">
                        {{ __('messages.admin.new_tenant') }}
                    </a>
                    <span class="text-muted small d-none d-md-inline">{{ Auth::guard('admin')->user()->name }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm">
                            {{-- <iconify-icon icon="solar:logout-2-outline" class="me-1"></iconify-icon> --}}
                            {{ __('messages.admin.logout') }}
                        </button>
                    </form>
                </div>
            </header>

            <div class="admin-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold">{{ __('messages.admin.confirm_action') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.admin.cancel') }}"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="confirmActionMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        {{ __('messages.admin.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" id="confirmActionSubmit">
                        {{ __('messages.admin.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            let pendingConfirmForm = null;
            const confirmModalElement = document.getElementById('confirmActionModal');
            const confirmModal = confirmModalElement ? new bootstrap.Modal(confirmModalElement) : null;
            const confirmMessage = document.getElementById('confirmActionMessage');
            const confirmSubmit = document.getElementById('confirmActionSubmit');

            $('.js-confirm-form').on('submit', function (event) {
                if (! confirmModal) {
                    return true;
                }

                event.preventDefault();
                pendingConfirmForm = this;
                confirmMessage.textContent = this.dataset.confirmMessage || '';
                confirmModal.show();
            });

            confirmSubmit?.addEventListener('click', function () {
                if (! pendingConfirmForm) {
                    return;
                }

                confirmModal.hide();
                HTMLFormElement.prototype.submit.call(pendingConfirmForm);
                pendingConfirmForm = null;
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
