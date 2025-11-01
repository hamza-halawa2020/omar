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
    <!-- jQuery UI js -->
    <script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
    <!-- Data Table js -->
    <script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
    <!-- Popup js -->
    <script src="{{ asset('assets/js/lib/magnifc-popup.min.js') }}"></script>
    <!-- Axios js -->
    <script src="{{ asset('assets/js/axios.min.js') }}"></script>
    <!-- Chart.js -->
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <!-- sortable.min.js -->
    <script src="{{ asset('assets/js/sortable.min.js') }}"></script>
    <!-- Slick Slider js -->
    <script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
    <!-- Bootstrap Select js -->
    <script src="{{ asset('assets/js/lib/bootstrap-select.min.js') }}"></script>
    <!-- Multi Select js -->
    <script src="{{ asset('assets/js/lib/multi-select.js') }}"></script>
    <!-- Iconify Font js -->
    <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
    <!-- Toaster JS -->
    <script src="{{ asset('assets/js/lib/toaster.js') }}"></script>
    <!-- Flatpickr JS -->
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>

    <!-- Main Application JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>


    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        <button type="button" class="btn-close m-auto text-end" data-bs-dismiss="toast" aria-label="Close"><iconify-icon icon="mdi:close"></iconify-icon></button>
                    </div>
                </div>
            `;

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
