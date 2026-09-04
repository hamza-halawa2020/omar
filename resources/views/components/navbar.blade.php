<div class="navbar-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                </button>

                @can('whatsapp_index')
                    <div class="bg-success text-white px-3 py-2 rounded text-center">
                        <a href="https://web.whatsapp.com/" target="_blank" class="text-white text-decoration-none fw-bold">
                            <i class="ri-whatsapp-line"></i> WhatsApp
                        </a>
                    </div>
                    @if (Auth::user()?->whatsapp_api_token)
                        <div id="whatsappSenderStatus" class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-primary-100 text-primary-600 px-3 py-2 fw-semibold"
                                style="font-size: 0.95rem; line-height: 1.2;">
                                {{ __('messages.whatsapp_points') }}:
                                <strong data-whatsapp-points>...</strong>
                            </span>
                            <span class="badge bg-warning-100 text-warning-600 px-3 py-2 fw-semibold"
                                style="font-size: 0.95rem; line-height: 1.2;">
                                {{ __('messages.whatsapp_daily_remaining') }}:
                                <strong data-whatsapp-daily-remaining>...</strong>
                                /
                                <span data-whatsapp-daily-limit>...</span>
                            </span>
                        </div>
                    @endif
                @endcan

            </div>
        </div>
        <div class="col-auto">

            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" data-theme-toggle
                    class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>

                <div class="dropdown">
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                        data-bs-toggle="dropdown">
                        @php
                            $user = Auth::user();
                            $profileImage = $user?->profile_image
                                ? asset('storage/' . $user->profile_image)
                                : asset('assets/images/1.png');
                        @endphp
                        <img src="{{ $profileImage }}"
                            alt="image" class="w-40-px h-40-px object-fit-cover rounded-circle">

                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div
                            class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <a href="{{ route('dashboard.profile.index') }}">

                                    <div class="text-lg text-primary-light fw-semibold mb-2">
                                        {{ Auth::user()?->name ?? Auth::guard('admin')->user()?->name ?? 'Guest' }}
                                    </div>
                                </a>
                            </div>
                        </div>
                        <ul class="to-top-list">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3 bg-transparent border-0">
                                        <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                                        {{ __('messages.logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (Auth::user()?->whatsapp_api_token)
    @push('scripts')
        <script>
            $(function () {
                const $status = $('#whatsappSenderStatus');
                if (!$status.length) {
                    return;
                }

                $.get("{{ route('dashboard.whatsapp.status') }}")
                    .done(function (res) {
                        const data = res.data || {};
                        $status.find('[data-whatsapp-points]').text(data.walletPoints ?? 0);
                        $status.find('[data-whatsapp-daily-remaining]').text(data.remainingDailyLimit ?? '-');
                        $status.find('[data-whatsapp-daily-limit]').text(data.dailyLimit || '{{ __('messages.no') }}');
                    })
                    .fail(function () {
                        $status.addClass('d-none');
                    });
            });
        </script>
    @endpush
@endif
