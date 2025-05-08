@extends('dashboard.layouts.app')

@section('content')
<div class="container mt-4">
    @include('dashboard.layouts.flash')

    <h1>Create Role</h1>

    <form action="{{ route('roles.store') }}" method="POST" class="mt-3">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Role Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter role name">
        </div>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Assign Permissions</h5>
            <button type="button" class="btn btn-sm btn-outline-secondary m-3" id="selectAllBtn">Select All</button>
        </div>
        <div class="row">
            @foreach($permissions as $permission)
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input permission-checkbox m-2" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission{{ $permission->id }}">
                        <label class="form-check-label" for="permission{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-info mt-4">Save</button>
    </form>
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
