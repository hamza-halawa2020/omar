@extends('dashboard.layouts.app')

@section('content')
@include('components.alert')

    <div class="container mt-5">
        {{-- @include('dashboard.layouts.flash') --}}

        <div class="card shadow-lg border-0 rounded-3 p-3">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0"><i class="fas fa-user-shield me-2"></i> Edit Role: {{ $role->name }}</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-primary me-2" id="globalSelectAllBtn"><i
                            class="fas fa-check-circle me-1"></i>Select All</button>
                    <button type="button" class="btn btn-sm btn-secondary" id="globalUnselectAllBtn"><i
                            class="fas fa-times-circle me-1"></i>Unselect All</button>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Role Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">Role Name</label>

                        <input  @readonly(!$role->is_editable) type="text" name="name" id="name" value="{{ $role->name }}"
                            class="form-control rounded-3" required>
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Permissions Accordion -->
                    <div class="mb-3 fw-bold text-primary">Assign Permissions</div>
                    <div class="accordion" id="permissionsAccordion">
                        @php
                            // Group permissions by prefix
                            $groupedPermissions = [];
                            $prefixes = ['general_', 'hr_', 'academy_', 'crm_'];
                            foreach ($permissions as $permission) {
                                $prefix = 'other';
                                foreach ($prefixes as $p) {
                                    if (str_starts_with($permission->name, $p)) {
                                        $prefix = $p;
                                        break;
                                    }
                                }
                                $groupedPermissions[$prefix][] = $permission;
                            }
                        @endphp

                        @foreach ($groupedPermissions as $prefix => $perms)
                            <div class="accordion-item border-0 rounded-3 mb-3 shadow-sm">
                                <div class="accordion-header d-flex gap-2 align-items-center"
                                    id="heading_{{ $prefix }}">
                                    <button class="fw-bold" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse_{{ $prefix }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="collapse_{{ $prefix }}">
                                        <span class="text-primary">
                                            <i class="fas fa-cog me-2"></i>{{ ucfirst(str_replace('_', ' ', $prefix)) }}
                                            Permissions
                                        </span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary select-section rounded-pill"
                                        data-section="{{ $prefix }}"><i class="fas fa-check"></i></button>
                                    <button type="button" class="btn btn-sm btn-secondary unselect-section rounded-pill"
                                        data-section="{{ $prefix }}"><i class="fas fa-times"></i></button>
                                </div>

                                <div id="collapse_{{ $prefix }}"
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    aria-labelledby="heading_{{ $prefix }}" data-bs-parent="#permissionsAccordion">

                                    <div class="accordion-body">

                                        <div class="row">
                                            @foreach ($perms as $permission)
                                                <div class="col-md-4 col-sm-6 mb-3">
                                                    <div class="form-check fw-bold d-flex align-items-center gap-2">
                                                        <input class="form-check-input permission-checkbox" type="checkbox"
                                                            name="permissions[]" value="{{ $permission->id }}"
                                                            id="permission_{{ $permission->id }}"
                                                            data-section="{{ $prefix }}"
                                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                        <label class="form-check-label "
                                                            for="permission_{{ $permission->id }}"
                                                            title="{{ ucfirst(str_replace('_', ' ', $permission->name)) }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top">
                                                            {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">Update Role</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary px-4 ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Global Select/Unselect All
            $('#globalSelectAllBtn').click(function() {
                $('.permission-checkbox').prop('checked', true).fadeIn(200);
            });

            $('#globalUnselectAllBtn').click(function() {
                $('.permission-checkbox').prop('checked', false).fadeIn(200);
            });

            // Section-specific Select/Unselect
            $('.select-section').click(function() {
                var section = $(this).data('section');
                $(`.permission-checkbox[data-section="${section}"]`).prop('checked', true).fadeIn(200);
            });

            $('.unselect-section').click(function() {
                var section = $(this).data('section');
                $(`.permission-checkbox[data-section="${section}"]`).prop('checked', false).fadeIn(200);
            });
        });
    </script>
@endpush
