<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('dashboard.index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/1.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/1.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/1.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">

            @can('categories_index')
                <li>
                    <a href="{{ route('categories.index') }}"
                        class="d-flex align-items-center gap-2 {{ Route::is('categories.*') ? 'active-page' : '' }}">
                        <iconify-icon icon="mdi:view-grid-outline" class="menu-icon"></iconify-icon>
                        <span>{{ __('messages.categories') }}</span>
                    </a>
                </li>
            @endcan

            @can('payment_ways_index')
                <li>
                    <a href="{{ route('payment_ways.index') }}"
                        class="d-flex align-items-center gap-2 {{ Route::is('payment_ways.*') ? 'active-page' : '' }}">
                        <iconify-icon icon="mdi:credit-card-check-outline" class="menu-icon"></iconify-icon>
                        <span>{{ __('messages.payment_way') }}</span>
                    </a>
                </li>
            @endcan

            @can('clients_index')
                <li>
                    <a href="{{ route('clients.index') }}"
                        class="d-flex align-items-center gap-2 {{ Route::is('clients.*') ? 'active-page' : '' }}">
                        <iconify-icon icon="mdi:account-multiple-outline" class="menu-icon"></iconify-icon>
                        <span>{{ __('messages.clients') }}</span>
                    </a>
                </li>
            @endcan

            @can('clients_debts')
                <li>
                    <a href="{{ route('debts.index') }}"
                        class="d-flex align-items-center gap-2 {{ Route::is('debts.*') ? 'active-page' : '' }}">
                        <iconify-icon icon="mdi:cash-multiple" class="menu-icon"></iconify-icon>
                        <span>{{ __('messages.debts') }}</span>
                    </a>
                </li>
            @endcan

            @can('products_index')
                <li>
                    <a href="{{ route('products.index') }}"
                        class="d-flex align-items-center gap-2 {{ Route::is('products.*') ? 'active-page' : '' }}">
                        <iconify-icon icon="mdi:cart-outline" class="menu-icon"></iconify-icon>
                        <span>{{ __('messages.products') }}</span>
                    </a>
                </li>
            @endcan

            @can('installments_index')
                <li>
                    <a href="{{ route('installment_contracts.index') }}"
                        class="d-flex align-items-center gap-2 {{ Route::is('installment_contracts.*') ? 'active-page' : '' }}">
                        <iconify-icon icon="mdi:calendar-clock-outline" class="menu-icon"></iconify-icon>
                        <span>{{ __('messages.installments') }}</span>
                    </a>
                </li>
            @endcan

            @canany(['users_index', 'roles_index'])
                <li class="dropdown">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:cog-outline" class="menu-icon"></iconify-icon>
                        <span>{{ __('messages.settings') }}</span>
                    </a>

                    <ul class="sidebar-submenu">
                        @can('roles_index')
                            <li>
                                <a href="{{ route('roles.index') }}"
                                    class="d-flex align-items-center gap-2 {{ Route::is('roles.*') ? 'active-page' : '' }}">
                                    <iconify-icon icon="mdi:shield-account-outline" class="menu-icon"></iconify-icon>
                                    <span>{{ __('messages.roles') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('users_index')
                            <li>
                                <a href="{{ route('users.index') }}"
                                    class="d-flex align-items-center gap-2 {{ Route::is('users.*') ? 'active-page' : '' }}">
                                    <iconify-icon icon="mdi:account-outline" class="menu-icon"></iconify-icon>
                                    <span>{{ __('messages.users') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

        </ul>
    </div>
</aside>
