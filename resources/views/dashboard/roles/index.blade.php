@extends('dashboard.layouts.app')

@section('content')
@include('components.alert')

    <div class="container mt-4">
        {{-- @include('dashboard.layouts.flash') --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden p-3">
                        <div
                            class="card-header border-bottom py-3 px-5 d-flex align-items-center flex-wrap justify-content-between gap-3">
                            <div class="d-flex align-items-center flex-wrap gap-3">
                                <span class="text-secondary fw-medium p-2">Show</span>
                                <form action="{{ route('roles.index') }}" method="GET">
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
                                <form class="d-flex" action="{{ route('roles.index') }}" method="GET">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="search" placeholder="Search"
                                            value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <iconify-icon icon="ion:search-outline"></iconify-icon>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <a href="{{ route('roles.create') }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-2"><iconify-icon
                                    icon="ic:baseline-plus"></iconify-icon>Create Role</a>

                        </div>

                        @foreach ($roles as $role)
                            <div class="card mb-3">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $role->name }}</strong>
                                    </div>
                                    <div>
                                        <a href="{{ route('roles.edit', $role->id) }}"
                                            class="btn btn-sm btn-outline-primary radius-8"> <iconify-icon
                                                icon="lucide:edit" class="text-lg"></iconify-icon></a>

                                        @if (Str::lower($role->name) !== 'super admin')
                                        @if ($role->is_editable)
                                            <button type="button" class="btn btn-outline-danger btn-sm radius-8"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $role->id }}"
                                                data-id="{{ $role->id }}" data-name="{{ $role->name }}">

                                                <iconify-icon icon="fluent:delete-24-regular"
                                                    class="text-lg"></iconify-icon>

                                            </button>
                                        @endif
                                        @else
                                            <small class="border border-primary p-1 radius-8">Super Admin</small>
                                        @endif
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
                                                <h6 class="modal-title" id="deleteModalLabel-{{ $role->id }}">
                                                    Delete</h6>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this role?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                        <div class="m-2">
                            <div>
                                {{ $roles->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
