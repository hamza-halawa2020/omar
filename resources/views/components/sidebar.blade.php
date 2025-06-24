<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li>
                <a href="{{ route('dashboard') }}">
                    <iconify-icon icon="mdi:view-dashboard" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-menu-group-title">Application</li>
            @can('general_roles_index')
                <li>
                    <a href="{{ route('roles.index') }}"
                        class="d-flex align-items-center gap-2 {{ request()->is('roles/*') ? 'active-page' : '' }}">
                        <iconify-icon icon="mdi:shield-account" class="menu-icon"></iconify-icon>
                        <span>Roles</span>
                    </a>
                </li>
            @endcan
            {{-- @can('general_user_role_permissions_index')
                <li>
                    <a href="{{ route('user-role-permissions.index') }}"
                        class="d-flex align-items-center gap-2 {{ request()->is('user-role-permissions/*') ? 'active-page' : '' }}">
                        <iconify-icon icon="mdi:account-key" class="menu-icon"></iconify-icon>
                        <span>Assign</span>
                    </a>
                </li>
            @endcan --}}
        </ul>
    </div>
</aside>
