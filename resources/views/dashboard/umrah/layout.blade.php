@extends('dashboard.layouts.app')
@section('content')
    <div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="umrah-workspace">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <div class="mb-1">{{ __('umrah.ui_title') }}</div><span class="text-muted">{{ tenant('name') }} —
                    {{ __('umrah.ui_subtitle') }}</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-primary" href="{{ route('umrah.index') }}">{{ __('umrah.trips') }}</a>
                <a class="btn btn-outline-primary"
                    href="{{ route('umrah.travelers.index') }}">{{ __('umrah.travelers') }}</a>
                @can('umrah_reports_view')<a class="btn btn-outline-primary"
                href="{{ route('umrah.reports') }}">{{ __('umrah.reports') }}</a>@endcan
            </div>
        </div>
        @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">@foreach($errors->all() as $error)
                <li>{{ $error }}</li>@endforeach
                </ul>
        </div>@endif
        @yield('umrah')
    </div>
    <style>
        .umrah-workspace .card {
            margin-bottom: 1.25rem
        }

        .umrah-workspace .card-body {
            padding: 1.4rem
        }

        .umrah-workspace table {
            white-space: nowrap
        }

        .umrah-workspace label {
            margin-bottom: .4rem
        }

        .umrah-workspace details summary {
            cursor: pointer;
            padding: .5rem 0;
            font-weight: 600
        }

        .umrah-workspace .form-control,
        .umrah-workspace .form-select {
            background-color: var(--white, #fff)
        }

        @media print {

            .sidebar,
            .navbar-header,
            footer,
            .no-print,
            .umrah-workspace form,
            .umrah-workspace button {
                display: none !important
            }

            .dashboard-main {
                margin: 0 !important
            }

            .dashboard-main-body {
                padding: 0 !important
            }

            .umrah-workspace .table-responsive {
                overflow: visible
            }
        }
    </style>
@endsection