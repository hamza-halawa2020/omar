<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<x-head />

<body>

    <div class="position-fixed bottom-0 top-0 p-3" style="z-index: 11">
        <div id="toastContainer"></div>
    </div>

    <x-sidebar />

    <main class="dashboard-main">
        <x-navbar />
        <div class="dashboard-main-body">
            @yield('content')
        </div>
        <x-footer />

    </main>



    <!-- jQuery library js -->
    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap js -->
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <!-- Data Table js -->
    <script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
    <!-- Iconify Font js -->
    <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
    <!-- jQuery UI js -->
    <script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
    <!-- Popup js -->
    <script src="{{ asset('assets/js/lib/magnifc-popup.min.js') }}"></script>
    <!-- Slick Slider js -->
    <script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
    <!-- multi select -->
    <script src="{{ asset('assets/js/lib/multi-select.js') }}"></script>
    <!-- main js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <!-- Select2 JS -->
    <script src="{{ asset('assets/js/lib/select2.min.js') }}"></script>
    <!-- toaster JS -->
    <script src="{{ asset('assets/js/lib/toaster.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>


    <script>
        $(document).ready(function() {
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


    @stack('scripts')
</body>

</html>
