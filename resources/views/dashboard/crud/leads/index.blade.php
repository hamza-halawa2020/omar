@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3 p-3">
                    <div class="card-header py-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">Leads</h5>
                        <a href="{{ route('leads.create') }}"
                           class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Add New Lead
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            @if($leads->count() > 0)
                                <table class="table bordered-table mb-0 table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">First name</th>
                                        <th scope="col">Last name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Source</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Assigned To</th>
                                        <th scope="col">Notes</th>
                                        <th scope="col" class="text-center">Flag</th>
                                        <th scope="col" class="text-center">Last Follow Up</th>
                                        <th scope="col" class="text-center">Next Follow Up</th>
                                        <th scope="col" class="text-center">Is Follow Up</th>
                                        <th scope="col" class="text-center">Created At</th>
                                        <th scope="col" class="text-center">Updated At</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($leads as $lead)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a
                                                        href="{{ route('leads.edit', $lead->id) }}"
                                                        class="text-lg text-secondary-light fw-semibold flex-grow-1">{{ $lead->first_name }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $lead->last_name }}</td>
                                            <td>{{ $lead->email }}</td>
                                            <td>{{ $lead->phone }}</td>
                                            <td>{{ $lead->source }}</td>
                                            <td class="text-center">
                                                    <span class="px-24 py-4 rounded-pill fw-medium text-sm">
                                                        {{ str($lead->status->name)->replace('_', ' ') }}
                                                    </span>
                                            </td>
                                            <td class="text-center">{{ $lead->assignedUser->full_name }}</td>
                                            <td>{{ str($lead->notes)->limit(30) }}</td>
                                            <td class="text-center">
                                                    <span class="px-24 py-4 rounded-pill fw-medium text-sm
                                                        {{ $lead->flag === 'high' ? 'bg-danger-focus text-danger-main' :
                                                           ($lead->flag === 'medium' ? 'bg-warning-focus text-warning-main' :
                                                           'bg-secondary-focus text-secondary-main') }}">
                                                        {{ str($lead->flag)->title() }}
                                                    </span>
                                            </td>
                                            <td class="text-center">{{ $lead->last_follow_up ?? '-' }}</td>
                                            <td class="text-center">{{ $lead->next_follow_up ?? '-' }}</td>
                                            <td class="text-center">
                                                    <span class="px-24 py-4 rounded-pill fw-medium text-sm
                                                        {{ $lead->is_follow_up ? 'bg-success-focus text-success-main' : 'bg-danger-focus text-danger-main' }}">
                                                        {{ $lead->is_follow_up ? 'Yes' : 'No' }}
                                                    </span>
                                            </td>
                                            <td class="text-center">{{ $lead->created_at ?? '-' }}</td>
                                            <td class="text-center">{{ $lead->updated_at ?? '-' }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('leads.edit', $lead->id) }}"
                                                       class="btn btn-outline-primary btn-sm radius-8"
                                                       title="Edit Lead">
                                                        <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                                    </a>
                                                    <form action="{{ route('leads.destroy', ['lead' => $lead->id]) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Are you sure you want to delete this lead?');">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm radius-8"
                                                                title="Delete Lead">
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
                                    <i class="bi bi-info-circle me-2"></i> No leads exist
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-8 px-4">
            {{ $leads->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
