@extends('dashboard.layouts.app')

@section('content')
<div class="container mt-4">
    @include('dashboard.layouts.flash')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Permissions</h1>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">Create Permission</a>
    </div>

    @foreach ($permissions as $permission)
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $permission->name }}</strong>
                </div>
                <div>
                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('permissions.destroy', $permission->id) }}">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        let url = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('<form>', {
                    'method': 'POST',
                    'action': url
                })
                .append('@csrf')
                .append('@method("DELETE")')
                .appendTo('body')
                .submit();
            }
        });
    });
</script>
@endsection
