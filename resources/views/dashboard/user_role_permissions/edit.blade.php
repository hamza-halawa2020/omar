@extends('dashboard.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Assign Roles to {{ $user->full_name ?? ($user->name ?? 'User') }}</h2>

        @if (session('success'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($user->exists)
            <form action="{{ route('user-role-permissions.update', $user->id) }}" method="POST" class="card p-4 shadow-sm">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Roles</label>
                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" name="roles[]" id="role-{{ $role->id }}"
                                        value="{{ $role->id }}" class="form-check-input"
                                        {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                    <label for="role-{{ $role->id }}" class="form-check-label">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="access" class="form-label">Project Access</label>
                    @php
                        $access = json_decode($user->project_access, true);
                    @endphp
                    <select multiple class="form-select" id="access" name="project_access[]">
                        
                        <option value="hr" {{ in_array('hr', $access ?? []) ? 'selected' : '' }}>HR</option>
                        <option value="crm" {{ in_array('crm', $access ?? []) ? 'selected' : '' }}>CRM</option>
                        <option value="academy" {{ in_array('academy', $access ?? []) ? 'selected' : '' }}>Academy</option>
                        <option value="settings" {{ in_array('settings', $access ?? []) ? 'selected' : '' }}>Settings</option>
                    </select>
                
                    @error('project_access')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-blue">Update Roles</button>
                    <a href="{{ route('user-role-permissions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form> 
        @else
            <div class="alert alert-danger" role="alert">
                User not found.
            </div>
        @endif
    </div>


@endsection


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#access').select2({
            placeholder: "Select"
        });
    });
</script>




<style>
    .btn-blue {
        background-color: #4a90e2;
        border-color: #4a90e2;
        color: white;
    }

    .btn-blue:hover {
        background-color: #357abd;
        border-color: #357abd;
    }

    .alert-info {
        background-color: #e6f0fa;
        border-color: #b3d4fc;
        color: #004085;
    }

    .form-check-input:checked {
        background-color: #4a90e2;
        border-color: #4a90e2;
    }
</style>
