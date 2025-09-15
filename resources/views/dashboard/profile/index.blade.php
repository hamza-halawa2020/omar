@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container">

        <style>
            .profile-image {
                max-width: 150px;
                max-height: 150px;
                object-fit: cover;
                border-radius: 50%;
            }

            .preview-image {
                max-width: 100px;
                max-height: 100px;
                object-fit: cover;
                border-radius: 50%;
                display: none;
                margin-top: 10px;
            }
        </style>

        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">User Profile</div>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('images/default-profile.png') }}"
                                    class="profile-image" alt="Profile Image">
                            </div>
                            <form id="profileForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ auth()->user()->email }}" required>
                                </div>

                                <div class="position-relative mb-20">
                                    <div class="icon-field">
                                        <span class="icon top-50 translate-middle-y">
                                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                                        </span>
                                        <input type="password" name="password"
                                            class="form-control h-56-px bg-neutral-50 radius-12 pe-50" id="your-password"
                                            placeholder="Password" required>

                                        <span class="toggle-password position-absolute top-50 end-0 translate-middle-y me-3"
                                            style="cursor: pointer;">
                                            <iconify-icon icon="mdi:eye-off-outline" class="show-icon"></iconify-icon>
                                            <iconify-icon icon="mdi:eye-outline" class="hide-icon"
                                                style="display: none;"></iconify-icon>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image"
                                        accept="image/*">
                                    <img id="imagePreview" class="preview-image" alt="Image Preview">
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                            <div id="successMessage" class="alert alert-success mt-3" style="display: none;">
                                Profile updated successfully!
                            </div>
                            <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;">
                                An error occurred while updating the profile.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
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


            // Preview image before upload
            $('#profile_image').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').hide();
                }
            });

            // Handle form submission with AJAX
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ route('dashboard.profile.update') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function(response) {
                        $('#successMessage').show().fadeOut(5000);
                        // Update profile image if new one was uploaded
                        if (response.profile_image) {
                            $('.profile-image').attr('src', '/storage/' + response.profile_image);
                        }
                    },
                    error: function(xhr) {
                        $('#errorMessage').text(xhr.responseJSON?.message || 'An error occurred while updating the profile.').show().fadeOut(5000);
                    }
                });
            });
        });

    </script>
@endpush
