@extends('dashboard.layouts.app')

@section('content')
    @include('components.alert')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 border-0 shadow-sm overflow-hidden p-3">
                    <div class="card-header border-bottom d-flex align-items-center flex-wrap justify-content-between gap-3">
                        <form action="{{ route('tabs.index') }}" method="GET">
                            <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                @foreach ([5, 10, 25, 50, 100, 150] as $count)
                                    <option value="{{ $count }}"
                                        {{ request('per_page', 10) == $count ? 'selected' : '' }}>
                                        {{ $count }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        <form class="d-flex" action="{{ route('tabs.index') }}" method="GET">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="search" placeholder="Search"
                                    value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <iconify-icon icon="ion:search-outline"></iconify-icon>
                                </button>
                            </div>
                        </form>

                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                             Add Parent Tab 
                        </button>

                    </div>

                    <div class="table-responsive">
                        <!-- tabs Table -->
                        <table class="table table-bordered table-sm table bordered-table sm-table mb-0">

                            <thead class="">
                                <tr>
                                    <th>Id</th>
                                    <th class=>Label</th>
                                    <th class=>Icon</th>
                                    <th class=>Parent</th>
                                    <th class=>Order</th>
                                    <th>Assign</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tabs as $tab)
                                    <tr>
                                        <td>{{ $tab->id }}</td>
                                        <td>{{ $tab->label }}</td>
                                        <td>{{ $tab->icon }}</td>
                                        <td>{{ $tab->parent->label ?? '' }}</td>
                                        <td>{{ $tab->order }}</td>
                                        <td>
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-outline-primary btn-sm radius-8"
                                                data-bs-toggle="modal" data-bs-target="#editModal{{ $tab->id }}">
                                                <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>

                                            </button>
                                        </td>
                                    </tr>


                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $tab->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('tabs.update', $tab->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <div class="modal-title">Edit Tab</div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="label" class="form-label">Label</label>
                                                            <input type="text" class="form-control" name="label"
                                                                value="{{ $tab->label }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="icon" class="form-label">Icon</label>
                                                            <input type="text" class="form-control" name="icon"
                                                                value="{{ $tab->icon }}">
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="parent_id" class="form-label">Parent Tab</label>
                                                            <select name="parent_id" class="form-select">
                                                                <option value="">None</option>
                                                                @foreach ($tabs->where('url', null)->where('id', '!=', $tab->id) as $parent)
                                                                    <option value="{{ $parent->id }}"
                                                                        {{ $tab->parent_id == $parent->id ? 'selected' : '' }}>
                                                                        {{ $parent->label }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="order" class="form-label">Order</label>
                                                            <input type="number" class="form-control" name="order"
                                                                value="{{ $tab->order }}">
                                                        </div>
                                                    </div>


                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No tabs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="m-2">
                        <div>
                            {{ $tabs->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
