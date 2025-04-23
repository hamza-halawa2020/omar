@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3 p-3">
                    <div class="card-header py-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">work Flows</h5>
                        <a href="{{ route('workflow.create') }}"
                            class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Add New Work Flow
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            @if ($workflows->count() > 0)
                                <table class="table bordered-table mb-0 table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Type</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($workflows as $flow)
                                            <tr>
                                                <td>{{ $flow->name }}</td>
                                                <td>{{ $flow->type }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="{{ route('workflow.edit', $flow->id) }}"
                                                            class="btn btn-outline-primary btn-sm radius-8"
                                                            title="Edit Contact">
                                                            <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                                        </a>
                                                        <form
                                                            action="{{ route('workflow.destroy', ['workflow' => $flow->id]) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-outline-danger btn-sm radius-8"
                                                                title="Delete Contact">
                                                                <iconify-icon icon="fluent:delete-24-regular"
                                                                    class="text-lg"></iconify-icon>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert text-center rounded-3 shadow-sm mt-8" role="alert">
                                    <i class="bi bi-info-circle me-2"></i> No work flow exist
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-8 px-4">
            {{ $workflows->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
