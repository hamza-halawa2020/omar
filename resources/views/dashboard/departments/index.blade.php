@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 border-0 shadow-sm overflow-hidden p-3">
                    <div
                        class="card-header border-bottom py-3 px-5 d-flex align-items-center flex-wrap justify-content-between gap-3">



                        <form action="{{ route('departments.index') }}" method="GET">
                            <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                @foreach ([5, 10, 25, 50, 100, 150] as $count)
                                    <option value="{{ $count }}"
                                        {{ request('per_page', 10) == $count ? 'selected' : '' }}>
                                        {{ $count }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        <form class="d-flex" action="{{ route('departments.index') }}" method="GET">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="search" placeholder="Search"
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <iconify-icon icon="ion:search-outline"></iconify-icon>
                                </button>
                            </div>
                        </form>

                    </div>

                    <!-- Departments Table -->
                    <table class="table table-bordered table-sm table bordered-table sm-table mb-0">

                        <thead class="table-dark">
                            <tr>
                                <th>Id</th>
                                <th class="d-flex">Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($departments as $department)
                                <tr>
                                    <td>{{ $department->id }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="{{ route('departments.edit', $department->id) }}"
                                            class="btn btn-sm btn-outline-primary radius-8"> <iconify-icon
                                                icon="lucide:edit" class="text-lg"></iconify-icon></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No departments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="m-2">
                        <div>
                            {{ $departments->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
