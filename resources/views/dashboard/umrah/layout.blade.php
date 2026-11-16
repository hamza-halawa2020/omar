@extends('dashboard.layouts.app')
@section('title', __('umrah.ui_title'))
@section('content')
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="umrah-workspace" data-last-form="{{ old('_umrah_form', '') }}">
    <header class="umrah-header">
        <div class="umrah-heading">
            @hasSection('umrah_back')
                <a class="umrah-back" href="@yield('umrah_back')" aria-label="{{ __('umrah.back') }}"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m14 5-7 7 7 7"/></svg></a>
            @endif
            <div><div>@yield('umrah_title', __('umrah.ui_title'))</div><p>{{ tenant('name') }}</p></div>
        </div>
        <nav class="umrah-header-links d-flex gap-2" aria-label="{{ __('umrah.mobile_navigation') }}">
            <a class="btn btn-outline-primary" href="{{ route('umrah.index') }}">{{ __('umrah.trips') }}</a>
            <a class="btn btn-outline-primary" href="{{ route('umrah.travelers.index') }}">{{ __('umrah.travelers') }}</a>
            @can('umrah_reports_view')<a class="btn btn-outline-primary" href="{{ route('umrah.reports') }}">{{ __('umrah.reports') }}</a>@endcan
        </nav>
    </header>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert" tabindex="-1" data-umrah-errors="{{ json_encode($errors->keys()) }}"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('umrah')
</div>
@endsection
