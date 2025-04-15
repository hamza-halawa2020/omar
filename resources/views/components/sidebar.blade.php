<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li>
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-menu-group-title">Application</li>

            <li>
                <a href="{{ route('leads.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('dashboard/leads/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:account-search-outline" class="menu-icon"></iconify-icon>
                    <span>Leads</span>
                </a>
            </li>

            <li>
                <a href="{{ route('accounts.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('dashboard/accounts/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:briefcase-account-outline" class="menu-icon"></iconify-icon>
                    <span>Accounts</span>
                </a>
            </li>

            <li>
                <a href="{{ route('contacts.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('dashboard/contacts/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:account-box-outline" class="menu-icon"></iconify-icon>
                    <span>Contacts</span>
                </a>
            </li>

            <li>
                <a href="{{ route('tasks.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('dashboard/tasks/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:check-outline" class="menu-icon"></iconify-icon>
                    <span>Tasks</span>
                </a>
            </li>

            <li>
                <a href="{{ route('deals.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('dashboard/deals/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:handshake-outline" class="menu-icon"></iconify-icon>
                    <span>Deals</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
