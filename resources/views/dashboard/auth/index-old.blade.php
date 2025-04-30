@extends('dashboard.layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div id="login-container" class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <form id="login-form">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Enter your email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Enter your password">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="login-button">Login</button>
                            </div>
                        </form>
                        <div id="error-message" class="alert alert-danger mt-3 d-none"></div>
                    </div>
                </div>
                <div id="project-links" class="mt-4 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-center mb-0">Access Other Projects</h3>
                        <button id="logout-button" class="btn btn-danger">Logout</button>
                    </div>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Project 1</h5>
                                    <button id="project1-link" class="btn btn-outline-primary">Go to Project</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Project 2</h5>
                                    <button id="project2-link" class="btn btn-outline-primary">Go to Project</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Project 3</h5>
                                    <button id="project3-link" class="btn btn-outline-primary">Go to Project</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Project 4</h5>
                                    <button id="project4-link" class="btn btn-outline-primary">Go to Project</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('styles')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
@endsection

@section('scripts')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <script>
        console.log('Script loaded');
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const form = e.target;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('error-message');
            const loginButton = document.getElementById('login-button');
            const loginContainer = document.getElementById('login-container');
            const projectLinks = document.getElementById('project-links');

            if (loginButton.disabled) return;

            errorMessage.classList.add('d-none');
            errorMessage.textContent = '';
            loginButton.disabled = true;
            loginButton.textContent = 'Logging in...';

            try {
                console.log('Sending login request...');
                const response = await fetch('http://upedia-general.test/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        email,
                        password
                    }),
                });

                const data = await response.json();
                if (response.ok) {
                    console.log('Login successful, token:', data.token);
                    localStorage.setItem('auth_token', data.token);

                    console.log('Creating session...');
                    const sessionResponse = await fetch('/create-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            token: data.token
                        }),
                    });

                    const sessionData = await sessionResponse.json();
                    if (sessionResponse.ok && sessionData.success) {
                        console.log('Session created, showing project links');
                        loginContainer.classList.add('d-none');
                        projectLinks.classList.remove('d-none');

                        const projects = [{
                                id: 'project1-link',
                                url: 'http://project1.test/create-session'
                            },
                            {
                                id: 'project2-link',
                                url: 'http://upedia-hr.test/create-session'
                            },
                            {
                                id: 'project3-link',
                                url: 'http://project3.test/create-session'
                            },
                            {
                                id: 'project4-link',
                                url: 'http://project4.test/create-session'
                            },
                        ];

                        projects.forEach(project => {
                            const link = document.getElementById(project.id);
                            if (link) {
                                console.log('Adding event listener for:', project.id);
                                link.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    console.log('Link clicked:', project.id, 'Navigating to:',
                                        project.url);
                                    const token = localStorage.getItem('auth_token');
                                    if (token) {
                                        window.open(
                                            `${project.url}?token=${encodeURIComponent(token)}`,
                                            '_blank');
                                    } else {
                                        console.error('No token found in localStorage');
                                        errorMessage.textContent =
                                            'No authentication token found';
                                        errorMessage.classList.remove('d-none');
                                        loginContainer.classList.remove('d-none');
                                        projectLinks.classList.add('d-none');
                                    }
                                });
                            } else {
                                console.error('Link not found:', project.id);
                            }
                        });

                        // Logout button
                        const logoutButton = document.getElementById('logout-button');
                        logoutButton.addEventListener('click', async (e) => {
                            e.preventDefault();
                            console.log('Logout requested from main project');
                            try {
                                const token = localStorage.getItem('auth_token');
                                if (token) {
                                    console.log('Sending logout request to main project...');
                                    const logoutResponse = await fetch(
                                        'http://upedia-general.test/api/logout', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'Authorization': `Bearer ${token}`,
                                                'X-CSRF-TOKEN': document.querySelector(
                                                    'meta[name="csrf-token"]').content,
                                            },
                                        });
                                    const logoutData = await logoutResponse.json();
                                    console.log('Main project logout response:', logoutData);
                                    if (!logoutResponse.ok) {
                                        console.error('Main project logout failed:', logoutData
                                            .error);
                                    }
                                } else {
                                    console.warn('No token found in localStorage for logout');
                                }

                                // Clear localStorage
                                localStorage.removeItem('auth_token');
                                console.log('localStorage cleared');

                                // Show login form and redirect
                                loginContainer.classList.remove('d-none');
                                projectLinks.classList.add('d-none');
                                console.log('Redirecting to login');
                                window.location.href = '/login';
                            } catch (error) {
                                console.error('Logout error:', error);
                                errorMessage.textContent = 'Logout failed: ' + error.message;
                                errorMessage.classList.remove('d-none');
                            }
                        });
                    } else {
                        errorMessage.textContent = sessionData.error || 'Failed to create session';
                        errorMessage.classList.remove('d-none');
                        console.error('Session creation failed:', sessionData.error);
                    }
                } else {
                    errorMessage.textContent = data.error || 'Invalid credentials';
                    errorMessage.classList.remove('d-none');
                    console.error('Login failed:', data.error);
                }
            } catch (error) {
                errorMessage.textContent = 'An error occurred: ' + error.message;
                errorMessage.classList.remove('d-none');
                console.error('Error:', error);
            } finally {
                loginButton.disabled = false;
                loginButton.textContent = 'Login';
            }
        });
    </script>
@endsection
