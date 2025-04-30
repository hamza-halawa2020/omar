@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <!-- CRM Card -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/logo.jpg') }}" alt="CRM Icon" class="mb-3"
                            style="width: 40px; height: 40px;">
                        <h5 class="card-title fw-bold">CRM</h5>
                        <p class="card-text text-muted">Comprehensive CRM platform for customer-facing teams.</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <!-- Mail Card -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/mail-icon.png') }}" alt="Mail Icon" class="mb-3"
                            style="width: 40px; height: 40px;">
                        <h5 class="card-title fw-bold">Mail</h5>
                        <p class="card-text text-muted">Secure email service for teams of all sizes.</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <!-- Desk Card -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/desk-icon.png') }}" alt="Desk Icon" class="mb-3"
                            style="width: 40px; height: 40px;">
                        <h5 class="card-title fw-bold">Desk</h5>
                        <p class="card-text text-muted">Helpdesk software to deliver great customer support.</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <!-- Assist Card -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/assist-icon.png') }}" alt="Assist Icon" class="mb-3"
                            style="width: 40px; height: 40px;">
                        <h5 class="card-title fw-bold">Assist</h5>
                        <p class="card-text text-muted">Remote support and unattended remote access software.</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <!-- Books Card -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/books-icon.png') }}" alt="Books Icon" class="mb-3"
                            style="width: 40px; height: 40px;">
                        <h5 class="card-title fw-bold">Books</h5>
                        <p class="card-text text-muted">Powerful accounting platform for growing businesses.</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <!-- Analytics Card -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/analytics-icon.png') }}" alt="Analytics Icon" class="mb-3"
                            style="width: 40px; height: 40px;">
                        <h5 class="card-title fw-bold">Analytics</h5>
                        <p class="card-text text-muted">Modern self-service BI and analytics platform.</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <!-- Creator Card -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/creator-icon.png') }}" alt="Creator Icon" class="mb-3"
                            style="width: 40px; height: 40px;">
                        <h5 class="card-title fw-bold">Creator</h5>
                        <p class="card-text text-muted">Build custom apps to simplify business processes.</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>

            <!-- Social Card -->
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/social-icon.png') }}" alt="Social Icon" class="mb-3"
                            style="width: 40px; height: 40px;">
                        <h5 class="card-title fw-bold">Social</h5>
                        <p class="card-text text-muted">All-in-one social media management software.</p>
                        <a href="#" class="text-primary fw-semibold text-decoration-none">TRY NOW
                            <span>&rarr;</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
