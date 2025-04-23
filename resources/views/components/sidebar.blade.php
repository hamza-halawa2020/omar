<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.jpg') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.jpg') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li>
                <a href="{{ route('home') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-menu-group-title">Application</li>

            <li>
                <a href="{{ route('leads.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('leads/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:account-search-outline" class="menu-icon"></iconify-icon>
                    <span>Leads</span>
                </a>
            </li>

            <li>
                <a href="{{ route('accounts.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('accounts/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:briefcase-account-outline" class="menu-icon"></iconify-icon>
                    <span>Accounts</span>
                </a>
            </li>

            <li>
                <a href="{{ route('contacts.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('contacts/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:account-box-outline" class="menu-icon"></iconify-icon>
                    <span>Contacts</span>
                </a>
            </li>

            <li>
                <a href="{{ route('tasks.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('tasks/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:check-outline" class="menu-icon"></iconify-icon>
                    <span>Tasks</span>
                </a>
            </li>

            <li>
                <a href="{{ route('deals.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('deals/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:handshake-outline" class="menu-icon"></iconify-icon>
                    <span>Deals</span>
                </a>
            </li>

            <li>
                <a href="{{ route('calls.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('calls/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:phone" class="menu-icon"></iconify-icon>
                    <span>calls</span>
                </a>
            </li>
            <li>
                <a href="{{ route('program_types.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('calls/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:phone" class="menu-icon"></iconify-icon>
                    <span>program types</span>
                </a>
            </li>

            <li>
                <a href="{{ route('workflow.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('calls/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:phone" class="menu-icon"></iconify-icon>
                    <span>WorkFlow</span>
                </a>
            </li>
            <li>
                <a href="{{ route('call_status.index') }}"
                   class="d-flex align-items-center gap-2 {{ request()->is('calls/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:phone" class="menu-icon"></iconify-icon>
                    <span>call status</span>
                </a>
            </li>
           
        </ul>
    </div>
</aside>
