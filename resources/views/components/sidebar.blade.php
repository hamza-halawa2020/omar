<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a class="sidebar-logo">

            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">

        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">

            {{-- @can('general_tabs_index') --}}
            <li>
                <a href="{{ route('categories.index') }}"
                    class="d-flex align-items-center gap-2 {{ request()->is('categories/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:format-list-bulleted-type" class="menu-icon"></iconify-icon>
                    <span>Categories</span>
                </a>
            </li>
            {{-- @endcan --}}

            {{-- @can('general_tabs_index') --}}
            <li>
                <a href="{{ route('payment_ways.index') }}"
                    class="d-flex align-items-center gap-2 {{ request()->is('payment_ways/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:format-list-bulleted-type" class="menu-icon"></iconify-icon>
                    <span>Payment Way</span>
                </a>
            </li>
            {{-- @endcan --}}
        </ul>

    </div>
</aside>
