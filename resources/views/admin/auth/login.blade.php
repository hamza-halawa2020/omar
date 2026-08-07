<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.admin.admin_login') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/1.png') }}" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link href="{{ asset('assets/css/lib/font-awesome/6.4.2.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>

    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="max-w-464-px mx-auto w-100 p-3">
            <div class="text-center mb-4">
                <a class="mb-3 d-inline-block" style="max-width:120px;">
                    <img src="{{ asset('assets/images/1.png') }}" alt="logo" style="width:100%;height:auto;object-fit:contain;">
                </a>
                <h5 class="fw-bold mt-3">{{ __('messages.admin.admin_login') }}</h5>
                <p class="text-secondary-light text-lg">{{ __('messages.admin.admin_login_subtitle') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.attempt') }}" method="POST">
                @csrf
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input type="email" name="email"
                        class="form-control h-56-px bg-neutral-50 radius-12"
                        placeholder="{{ __('messages.admin.email') }}"
                        value="{{ old('email') }}" required autofocus>
                </div>

                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password" name="password"
                            class="form-control h-56-px bg-neutral-50 radius-12 pe-50"
                            id="admin-password"
                            placeholder="{{ __('messages.admin.password') }}" required>
                        <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3"
                            style="cursor: pointer;">
                            <iconify-icon icon="mdi:eye-off-outline" class="show-icon"></iconify-icon>
                            <iconify-icon icon="mdi:eye-outline" class="hide-icon" style="display: none;"></iconify-icon>
                        </span>
                    </div>
                </div>

                <div class="mb-20">
                    <div class="form-check style-check d-flex align-items-center">
                        <input class="form-check-input border border-neutral-300" type="checkbox"
                            name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">{{ __('messages.admin.remember_me') }}</label>
                    </div>
                </div>

                <button type="submit"
                    class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12">
                    {{ __('messages.admin.login') }}
                </button>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.toggle-password').click(function () {
                const field = $('#admin-password');
                if (field.attr('type') === 'password') {
                    field.attr('type', 'text');
                    $('.show-icon').hide();
                    $('.hide-icon').show();
                } else {
                    field.attr('type', 'password');
                    $('.show-icon').show();
                    $('.hide-icon').hide();
                }
            });
        });
    </script>
</body>
</html>
