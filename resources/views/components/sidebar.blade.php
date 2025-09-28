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

            <li>
                <a href="{{ route('categories.index') }}"
                    class="d-flex align-items-center gap-2 {{ request()->is('categories/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:format-list-bulleted-type" class="menu-icon"></iconify-icon>
                    <span>{{ __('messages.categories') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ route('payment_ways.index') }}"
                    class="d-flex align-items-center gap-2 {{ request()->is('payment_ways/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:credit-card-outline" class="menu-icon"></iconify-icon>
                    <span>{{ __('messages.payment_way') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ route('clients.index') }}"
                    class="d-flex align-items-center gap-2 {{ request()->is('clients/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:users-outline" class="menu-icon"></iconify-icon>
                    <span>{{ __('messages.clients') }}</span>
                </a>
            </li>

            
            <li>
                <a href="{{ route('products.index') }}"
                    class="d-flex align-items-center gap-2 {{ request()->is('products/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:shopping-outline" class="menu-icon"></iconify-icon>
                    <span>{{ __('messages.products') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ route('installment_contracts.index') }}"
                    class="d-flex align-items-center gap-2 {{ request()->is('installment_contracts/*') ? 'active-page' : '' }}">
                    <iconify-icon icon="mdi:calendar-month-outline" class="menu-icon"></iconify-icon>
                    <span>{{ __('messages.installments') }}</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
