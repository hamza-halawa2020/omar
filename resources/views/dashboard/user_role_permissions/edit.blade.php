@extends('dashboard.layouts.app')

@section('content')
    <div class="container mt-5">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Content -->
        @if ($user->exists)
            <div class="card shadow-lg border-0 rounded-3 p-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user-shield me-2"></i>Assign Roles to {{ $user->full_name ?? ($user->name ?? 'User') }}
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('user-role-permissions.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Roles Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Roles</label>
                            <div class="row">
                                @foreach ($roles as $role)
                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <div class="form-check d-flex align-items-center gap-2">
                                            <input type="checkbox" name="roles[]" id="role-{{ $role->id }}"
                                                value="{{ $role->id }}" class="form-check-input"
                                                {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Assign {{ $role->name }} role">
                                            <label for="role-{{ $role->id }}" class="form-check-label">
                                                {{ ucfirst($role->name) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Project Access Section -->
                        <div class="mb-4">
                            <label for="access" class="form-label fw-bold text-dark">Project Access</label>
                            @php
                                $access = json_decode($user->project_access, true) ?? [];
                            @endphp
                            <select multiple class="form-select" id="access" name="project_access[]">
                                <option value="hr" {{ in_array('hr', $access) ? 'selected' : '' }}>HR</option>
                                <option value="crm" {{ in_array('crm', $access) ? 'selected' : '' }}>CRM</option>
                                <option value="academy" {{ in_array('academy', $access) ? 'selected' : '' }}>Academy</option>
                                <option value="settings" {{ in_array('settings', $access) ? 'selected' : '' }}>Settings</option>
                            </select>
                            @error('project_access')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Update Roles
                            </button>
                            <a href="{{ route('user-role-permissions.index') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-danger shadow-sm rounded-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>User not found.
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Custom Styles */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
        }

        .form-label {
            font-size: 1rem;
            color: #333;
        }

        .form-check-input:checked {
            background-color: #4a90e2;
            border-color: #4a90e2;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }

        .btn-primary {
            background-color: #4a90e2;
            border-color: #4a90e2;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #357abd;
            border-color: #357abd;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

        .alert-info {
            background-color: #e6f0fa;
            border-color: #b3d4fc;
            color: #004085;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 38px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #4a90e2;
            border: 1px solid #357abd;
            color: white;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ddd;
        }

        /* Responsive Adjustments */
        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .d-flex.gap-2 {
                flex-direction: column;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#access').select2({
                placeholder: 'Select Project Access',
                allowClear: true,
                width: '100%'
            });

            // Initialize Bootstrap Tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Ensure Select2 dropdown aligns with form styling
            $('.select2-container').addClass('w-100');
        });
    </script>
@endpush