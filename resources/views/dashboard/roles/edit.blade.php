@extends('dashboard.layouts.app')

@section('content')
<div class="container mt-5">
    @include('dashboard.layouts.flash')

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="form-label fw-bold">Role Name</label>
                    <input type="text" name="name" id="name" value="{{ $role->name }}" class="form-control" required>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Assign Permissions</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary m-3" id="selectAllBtn">Select All</button>
                </div>

                <div class="row">
                    @foreach($permissions as $permission)
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox m-2" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission{{ $permission->id }}"
                                    {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permission{{ $permission->id }}">
                                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-info mt-4">Update Role</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllBtn = document.getElementById('selectAllBtn');
        const checkboxes = document.querySelectorAll('.permission-checkbox');

        let allSelected = false;

        selectAllBtn.addEventListener('click', () => {
            allSelected = !allSelected;

            checkboxes.forEach(cb => {
                cb.checked = allSelected;
            });

            selectAllBtn.textContent = allSelected ? 'Unselect All' : 'Select All';
        });
    });
</script>
@endpush
