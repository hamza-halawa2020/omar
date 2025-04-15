@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3 p-3">
                    <div class="card-header bg-white py-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Deals</h5>
                        <a href="{{ route('deals.create') }}"
                           class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Add New Deal
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            @if($deals->count() > 0)
                                <table class="table bordered-table mb-0 table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Stage</th>
                                        <th scope="col">Contact id</th>
                                        <th scope="col">Account id</th>
                                        <th scope="col">Expected close date</th>
                                        <th scope="col" class="text-center">Created At</th>
                                        <th scope="col" class="text-center">Updated At</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($deals as $deal)
                                        <tr>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="text-lg text-secondary-light fw-semibold flex-grow-1">{{ $deal->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $deal->amount }}</td>
                                            <td>{{ $deal->stage }}</td>
                                            <td>{{ $deal->contact->name }}</td>
                                            <td>{{ $deal->account->name }}</td>
                                            <td>{{ $deal->expected_close_date }}</td>
                                            <td class="text-center">{{ $deal->created_at ?? '-' }}</td>
                                            <td class="text-center">{{ $deal->updated_at ?? '-' }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('deals.edit', $deal->id) }}"
                                                       class="btn btn-outline-primary btn-sm radius-8"
                                                       title="Edit Deal">
                                                        <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                                    </a>
                                                    <form
                                                        action="{{ route('deals.destroy', ['deal' => $deal->id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this deal?');">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm radius-8"
                                                                title="Delete Deal">
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
                                <div class="alert alert-light text-center rounded-3 shadow-sm mt-8" role="alert">
                                    <i class="bi bi-info-circle me-2"></i> No deals exist
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-8 px-4">
            {{ $deals->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
