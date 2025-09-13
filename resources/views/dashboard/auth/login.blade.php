@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="max-w-464-px mx-auto w-100 p-4">

            <div>
                <a href="" class="mb-40 max-w-290-px"><img src="{{ asset('assets/images/logo.png') }}"
                        alt=""></a>
                <div class="mb-12">Sign In to your Account</div>
                <p class="mb-32 text-secondary-light text-lg">Welcome back! please enter your detail</p>
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
                        placeholder="Email or Username" value="{{ old('login') }}" required>
                </div>

                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password" name="password" class="form-control h-56-px bg-neutral-50 radius-12 pe-50"
                            id="your-password" placeholder="Password" required>

                        <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3"
                            style="cursor: pointer;">
                            <iconify-icon icon="mdi:eye-off-outline" class="show-icon"></iconify-icon>
                            <iconify-icon icon="mdi:eye-outline" class="hide-icon" style="display: none;"></iconify-icon>
                        </span>
                    </div>
                </div>

                <div class="mb-20">
                    <div class="d-flex justify-content-between gap-2">
                        <div class="form-check style-check d-flex align-items-center">
                            <input class="form-check-input border border-neutral-300" type="checkbox" name="remember"
                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12">Sign In</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- تأكد إن layout عندك بيعمل @stack('scripts') --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        });
    </script>
@endpush
