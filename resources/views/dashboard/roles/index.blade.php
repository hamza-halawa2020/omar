@extends('dashboard.layouts.app')

@section('content')
    <div class="container mt-4">
        @include('dashboard.layouts.flash')

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Roles</h1>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
        </div>

        @foreach ($roles as $role)
            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $role->name }}</strong>
                    </div>
                    <div>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-info">Edit</a>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteModal-{{ $role->id }}">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal for this role -->
            <div class="modal fade" id="deleteModal-{{ $role->id }}" tabindex="-1"
                aria-labelledby="deleteModalLabel-{{ $role->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('roles.destroy', $role->id) }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel-{{ $role->id }}">Confirm Deletion</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this role?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
