@php
    use App\Enums\Calls\RelatedToType;
@endphp
<div class="row justify-content-center">
    <div class="py-4 px-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('calls.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle"></i> Add New Call
        </a>
    </div>
    <div class="table-responsive">
        @if ($calls->count() > 0)
            <table class="table bordered-table mb-0 table-hover">
                <thead>
                    <tr>
                        <th scope="col">Assigned to</th>
                        <th scope="col">Assigned to type</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Time</th>
                        <th scope="col">Duration in minutes</th>
                        <th scope="col" class="text-center">CallStatus</th>
                        <th scope="col" class="text-center">WorkFlow</th>
                        <th scope="col" class="text-center">Outcome</th>
                        <th scope="col" class="text-center">Notes</th>
                        <th scope="col" class="text-center">Created At</th>
                        <th scope="col" class="text-center">Updated At</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calls as $call)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('calls.edit', $call->id) }}"
                                        class="text-lg text-secondary-light fw-semibold flex-grow-1">
                                        {{ $call->relatedTo->name }}
                                    </a>
                                </div>
                            </td>
                            <td>{{ str(RelatedToType::from($call->related_to_type)->name)->lower() }}</td>
                            <td>{{ $call->subject }}</td>
                            <td>{{ $call->call_time }}</td>
                            <td>{{ $call->duration_in_minutes }}</td>
                            <td>{{ $call->callStatus->workFlow->name ?? '-' }}</td>
                            <td>{{ $call->callStatus->name ?? '-' }}</td>
                            <td>{{ str($call->outcome)->replace('_', ' ')->ucfirst() }}</td>
                            <td>{{ str($call->notes)->limit(30) }}</td>
                            <td class="text-center">{{ $call->created_at ?? '-' }}</td>
                            <td class="text-center">{{ $call->updated_at ?? '-' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('calls.edit', $call->id) }}"
                                        class="btn btn-outline-primary btn-sm radius-8" title="Edit Call">
                                        <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                    </a>
                                    <form action="{{ route('calls.destroy', ['call' => $call->id]) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this call?');">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm radius-8"
                                            title="Delete Call">
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
                <i class="bi bi-info-circle me-2"></i> No calls exist
            </div>
        @endif
    </div>
    <div class="py-8 px-4">
        {{ $calls->links('pagination::bootstrap-5') }}
    </div>
</div>
