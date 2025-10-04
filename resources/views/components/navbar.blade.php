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

                @can('roles_store')
                    
                <div class="bg-success text-white px-3 py-2 rounded text-center">
                    <a href="https://web.whatsapp.com/" target="_blank" class="text-white text-decoration-none fw-bold">
                        <i class="ri-whatsapp-line"></i> WhatsApp
                    </a>
                </div>
                @endcan

            </div>
        </div>
        <div class="col-auto">

            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="button" data-theme-toggle
                    class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>



                {{-- <div class="dropdown">
                    <button
                        class="position-relative w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
                        type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.7rem;">
                            5
                        </span>
                    </button>

                    <div class="dropdown-menu to-top dropdown-menu-lg p-0">
                        <div
                            class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <div class="text-lg text-primary-light fw-semibold mb-0">Notifications</div>
                            </div>
                            <span
                                class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">05</span>
                        </div>

                        <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
                            <a href="javascript:void(0)"
                                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                                <div
                                    class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                                    <span
                                        class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                                        <iconify-icon icon="bitcoin-icons:verify-outline"
                                            class="icon text-xxl"></iconify-icon>
                                    </span>
                                    <div>
                                        <div class="text-md fw-semibold mb-4">Congratulations</div>
                                        <p class="mb-0 text-sm text-secondary-light text-w-200-px">Your profile has been
                                            Verified. Your profile has been Verified</p>
                                    </div>
                                </div>
                                <span class="text-sm text-secondary-light flex-shrink-0">23 Mins ago</span>
                            </a>

                        </div>

                        <div class="text-center py-12 px-16">
                            <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md">See All
                                Notification</a>
                        </div>

                    </div>
                </div><!-- Notification dropdown end --> --}}


                <div class="dropdown">
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                        data-bs-toggle="dropdown">
                        @php
                            $user = Auth::user();
                            $profileImage = $user->staff->staff_photo ?? asset('assets/images/1.png');
                        @endphp
                        <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('images/default-profile.png') }}"
                            alt="image" class="w-40-px h-40-px object-fit-cover rounded-circle">

                    </button>
                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div
                            class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <a href="{{ route('dashboard.profile.index') }}">

                                    <div class="text-lg text-primary-light fw-semibold mb-2">
                                        {{ Auth::user()?->name ?? 'Guest' }}
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
