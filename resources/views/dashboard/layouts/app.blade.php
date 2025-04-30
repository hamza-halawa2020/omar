<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<x-head />

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body>
    @if (!Route::is('auth.login'))
        <x-sidebar />
    @endif

    <style>
        .dashboard-main.no-sidebar {
            /* margin-left: 0; */
        }

        .dashboard-main-body {
            /* padding: 20px; */
        }
    </style>
        <main class="{{ Route::is('auth.login') ? '' : 'dashboard-main' }}">

        @if (!Route::is('auth.login'))
            <x-navbar />
        @endif

        <div class="{{ Route::is('auth.login') ? '' : 'dashboard-main-body' }}">
            <x-breadcrumb title='{{ $title ?? '' }}' subTitle='{{ $subTitle ?? '' }}' />

            @yield('content')

        </div>
        <!-- ..::  footer  start ::.. -->
        <x-footer />
        <!-- ..::  footer area end ::.. -->

    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')


    <!-- ..::  scripts  start ::.. -->
    <x-script script='{!! isset($script) ? $script : '' !!}' />
    <!-- ..::  scripts  end ::.. -->

</body>

</html>
