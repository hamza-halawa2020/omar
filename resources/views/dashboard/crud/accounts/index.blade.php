@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3 p-3">
                    <div class="card-header py-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">Accounts</h5>
                        <a href="{{ route('accounts.create') }}"
                           class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Add New Account
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            @if($accounts->count() > 0)
                                <table class="table bordered-table mb-0 table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Industry</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col" class="text-center">Address</th>
                                        <th scope="col" class="text-center">Assigned To</th>
                                        <th scope="col" class="text-center">Created At</th>
                                        <th scope="col" class="text-center">Updated At</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($accounts as $account)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="text-lg text-secondary-light fw-semibold flex-grow-1">{{ $account->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $account->industry }}</td>
                                            <td>{{ $account->email }}</td>
                                            <td>{{ $account->phone }}</td>
                                            <td>{{ $account->address }}</td>
                                            <td class="text-center">{{ $account->assignedUser->full_name }}</td>
                                            <td class="text-center">{{ $account->created_at ?? '-' }}</td>
                                            <td class="text-center">{{ $account->updated_at ?? '-' }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('accounts.edit', $account->id) }}"
                                                       class="btn btn-outline-primary btn-sm radius-8"
                                                       title="Edit Account">
                                                        <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                                    </a>
                                                    <form action="{{ route('accounts.destroy', ['account' => $account->id]) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Are you sure you want to delete this account?');">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm radius-8"
                                                                title="Delete Account">
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
                                    <i class="bi bi-info-circle me-2"></i> No accounts exist
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-8 px-4">
            {{ $accounts->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
