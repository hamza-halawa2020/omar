@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="CRM Icon" class="mb-3"
                            style="width: 140px;">
                        <h5 class="card-title fw-bold">CRM</h5>
                        <p class="card-text text-muted">CRM platform for customer-facing teams.</p>
                        <a href="http://127.0.0.1:8001" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Mail Icon" class="mb-3"
                            style="width: 140px;">
                        <h5 class="card-title fw-bold">HR</h5>
                        <p class="card-text text-muted">Secure email service for teams of all sizes.</p>
                        <a href="http://127.0.0.1:8002" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Desk Icon" class="mb-3"
                            style="width: 140px;">
                        <h5 class="card-title fw-bold">Academy</h5>
                        <p class="card-text text-muted">software to deliver great customer support.</p>
                        <a href="http://127.0.0.1:8003" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

          
        </div>
    </div>
@endsection
