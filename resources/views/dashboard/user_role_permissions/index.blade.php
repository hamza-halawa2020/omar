@extends('dashboard.layouts.app')

@section('content')
@include('components.alert')

    <div class="container mt-4">
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
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden p-3">

                            <div
                                class="card-header border-bottom py-3 px-5 d-flex align-items-center flex-wrap justify-content-between gap-3">
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <span class="text-secondary fw-medium p-2">Show</span>
                                    <form action="{{ route('user-role-permissions.index') }}" method="GET">
                                        <select name="per_page" class="form-select form-select-sm w-auto"
                                            onchange="this.form.submit()">
                                            @foreach ([5, 10, 25, 50, 100, 150] as $count)
                                                <option value="{{ $count }}"
                                                    {{ request('per_page', 10) == $count ? 'selected' : '' }}>
                                                    {{ $count }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <form class="d-flex" action="{{ route('user-role-permissions.index') }}" method="GET">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" name="search" placeholder="Search"
                                                value="{{ request('search') }}">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <iconify-icon icon="ion:search-outline"></iconify-icon>
                                            </button>
                                        </div>
                                    </form>
                                </div>


                            </div>
                            <table class="table bordered-table sm-table mb-0">

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
                            <div class="m-2">
                                <div>
                                    {{ $users->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
