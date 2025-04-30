@extends('dashboard.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Manage User Roles</h2>

        @if (session('success'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($users->isEmpty())
            <div class="alert alert-info" role="alert">
                No users found.
            </div>
        @else
            <div class="">
                <div class="">
                    <div class="">
                            <table class="table table-bordered table-sm table bordered-table sm-table mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Roles</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @if ($user->exists)
                                        <tr>
                                            <td>{{ $user->full_name ?? ($user->name ?? 'N/A') }}</td>
                                            <td>{{ $user->email ?? 'N/A' }}</td>
                                            <td>
                                                @forelse ($user->roles as $role)
                                                    <span class="badge bg-blue me-1">{{ $role->name }}</span>
                                                @empty
                                                    <span class="text-muted badge bg-danger me-1">No roles</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                <a href="{{ route('user-role-permissions.edit', $user->id) }}"
                                                    class="btn btn-blue btn-sm btn-info">Edit</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="card-footer bg-white">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

<style>
    .bg-blue {
        background-color: #4a90e2;
        color: white;
    }

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
</style>
