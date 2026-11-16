<aside class="sidebar">
    <button type="button" class="sidebar-close-btn" aria-label="{{ __('umrah.close_menu') }}"><iconify-icon icon="mdi:close"></iconify-icon></button>
    <a href="{{ route('umrah.index') }}" class="sidebar-logo"><img src="{{ asset('assets/images/app-logo.svg') }}"
            alt="{{ __('umrah.umrah_management') }}"></a>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li><a href="{{ route('umrah.index') }}"
                    class="{{ request()->routeIs('umrah.index', 'umrah.trips.*') ? 'active-page' : '' }}"><iconify-icon
                        icon="mdi:airplane"
                        class="menu-icon"></iconify-icon><span>{{ __('umrah.trips_programs') }}</span></a></li>
            <li><a href="{{ route('umrah.travelers.index') }}"
                    class="{{ request()->routeIs('umrah.travelers.*') ? 'active-page' : '' }}"><iconify-icon
                        icon="mdi:account-group"
                        class="menu-icon"></iconify-icon><span>{{ __('umrah.travelers_passports') }}</span></a></li>
            @can('umrah_reports_view')
                <li><a href="{{ route('umrah.reports') }}"><iconify-icon icon="mdi:chart-box"
                            class="menu-icon"></iconify-icon><span>{{ __('umrah.reports_statements') }}</span></a></li>
            @endcan
            <li><a href="{{ route('dashboard.profile.index') }}">{{ __('umrah.profile_whatsapp') }}</a></li>
        </ul>
    </div>
</aside>
