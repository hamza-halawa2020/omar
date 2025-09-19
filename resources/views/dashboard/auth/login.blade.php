<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.login_title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/1.jpg') }}" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link href="{{ asset('assets/css/lib/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/lib/font-awesome/6.4.2.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>

    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="max-w-464-px mx-auto w-100 p-3">
            <div class="text-center">
                <a class="mb-3 max-w-290-px"><img src="{{ asset('assets/images/1.jpg') }}" alt=""></a>
                <div class="">{{ __('messages.sign_in_title') }}</div>
                <p class="mb-3 text-secondary-light text-lg">{{ __('messages.sign_in_subtitle') }}</p>
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

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.attempt') }}" method="POST">
                @csrf
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input type="text" name="email" class="form-control h-56-px bg-neutral-50 radius-12"
                        placeholder="{{ __('messages.email_or_username') }}" value="{{ old('login') }}" required>
                </div>

                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password" name="password"
                            class="form-control h-56-px bg-neutral-50 radius-12 pe-50" id="your-password"
                            placeholder="{{ __('messages.password') }}" required>

                        <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3"
                            style="cursor: pointer;">
                            <iconify-icon icon="mdi:eye-off-outline" class="show-icon"></iconify-icon>
                            <iconify-icon icon="mdi:eye-outline" class="hide-icon"
                                style="display: none;"></iconify-icon>
                        </span>
                    </div>
                </div>

                <div class="mb-20">
                    <div class="d-flex justify-content-between gap-2">
                        <div class="form-check style-check d-flex align-items-center">
                            <input class="form-check-input border border-neutral-300" type="checkbox" name="remember"
                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">{{ __('messages.remember_me') }}</label>
                        </div>
                    </div>
                </div>
                <button type="submit"
                    class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12">{{ __('messages.sign_in_button') }}</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/lib/toaster.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.toggle-password').click(function() {
                const passwordField = $('#your-password');
                const showIcon = $('.show-icon');
                const hideIcon = $('.hide-icon');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    showIcon.hide();
                    hideIcon.show();
                } else {
                    passwordField.attr('type', 'password');
                    showIcon.show();
                    hideIcon.hide();
                }
            });

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });

        function showToast(message, type = 'success') {
            let toastId = 'toast-' + Date.now();
            let bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';

            let toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0 mb-3" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>`;

            $('#toastContainer').append(toastHtml);
            let toastEl = new bootstrap.Toast(document.getElementById(toastId));
            toastEl.show();
            setTimeout(() => {
                $('#' + toastId).remove();
            }, 3500);
        }
    </script>
</body>

</html>
