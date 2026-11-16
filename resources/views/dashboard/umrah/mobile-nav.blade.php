<nav class="umrah-mobile-nav" aria-label="{{ __('umrah.mobile_navigation') }}">
    <a href="{{ route('umrah.index') }}"
        @if(request()->routeIs('umrah.index', 'umrah.trips.*', 'umrah.bookings.*', 'umrah.receipts.*')) aria-current="page"
        @endif>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="m3 10 18-7-7 18-3-8-8-3Z" />
            <path d="m11 13 5-5" />
        </svg><span>{{ __('umrah.trips') }}</span>
    </a>
    <a href="{{ route('umrah.travelers.index') }}" @if(request()->routeIs('umrah.travelers.*')) aria-current="page"
    @endif>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="9" cy="8" r="3" />
            <path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6m2 3a5 5 0 0 1 3 4v3" />
        </svg><span>{{ __('umrah.travelers') }}</span>
    </a>
    @can('umrah_reports_view')
        <a href="{{ route('umrah.reports') }}" @if(request()->routeIs('umrah.reports')) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M4 3v18h17M9 17v-6m5 6V7m5 10V4" />
            </svg><span>{{ __('umrah.reports') }}</span>
        </a>
    @endcan
    <a href="{{ route('dashboard.profile.index') }}" @if(request()->routeIs('dashboard.profile.*')) aria-current="page"
    @endif>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="8" r="4" />
            <path d="M4 22v-2a8 8 0 0 1 16 0v2" />
        </svg><span>{{ __('umrah.my_account') }}</span>
    </a>
</nav>