@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3 p-3">
                    <div class="card-header py-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">call status</h5>
                        <a href="{{ route('call_status.create') }}"
                            class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Add New call status
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            @if ($calls->count() > 0)
                                <table class="table bordered-table mb-0 table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">work flow</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($calls as $call)
                                            <tr>
                                                <td>{{ $call->name }}</td>
                                                <td>{{ $call->workFlow->name }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="{{ route('call_status.edit', $call->id) }}"
                                                            class="btn btn-outline-primary btn-sm radius-8"
                                                            title="Edit Contact">
                                                            <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                                        </a>
                                                        <form
                                                            action="{{ route('call_status.destroy', ['call_status' => $call->id]) }}"
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
                                    <i class="bi bi-info-circle me-2"></i> No work call status exist
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-8 px-4">
            {{ $calls->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
