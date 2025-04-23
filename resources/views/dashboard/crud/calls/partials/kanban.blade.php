@php
    use App\Enums\Calls\RelatedToType;
    $classes = [
        'default' => 'default-card',
    ];
@endphp

<div class="mb-4 w-100">
    <a href="{{ route('calls.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i> Add New Call
    </a>
</div>
<div class="overflow-x-auto scroll-sm pb-8">
    <div class="kanban-wrapper d-flex gap-24" style="flex-wrap: nowrap;">

        @forelse ($workflows as $workflow)
            <div class="workflow-section" style="min-width: 300px; max-width: 400px;">
                <div class="d-flex align-items-start gap-24" id="sortable-wrapper-{{ $workflow->id }}">
                    @if ($workflow->callStatus->isEmpty())
                        <div class="kanban-item radius-12 {{ $classes['default'] }}"
                            data-workflow-id="{{ $workflow->id }}" style="min-width: 250px;">
                            <div class="card p-0 radius-12">
                                <div class="card-body p-0 pb-24">
                                    <div
                                        class="d-flex align-items-center gap-2 justify-content-between ps-24 pt-24 pe-24 card-header">
                                        <h6 class="text-lg fw-semibold mb-0">{{ $workflow->name }}</h6>
                                        <div class="d-flex align-items-center gap-3 justify-content-between mb-0">
                                            <button type="button" class="text-2xl hover-text-primary add-task-button">
                                                <iconify-icon icon="ph:plus-circle" class="icon"></iconify-icon>
                                            </button>
                                            <div class="dropdown">
                                                <button type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <iconify-icon icon="entypo:dots-three-vertical"
                                                        class="text-xl"></iconify-icon>
                                                </button>
                                                <ul class="dropdown-menu p-12 border bg-base shadow">
                                                    <li>
                                                        <a class="duplicate-button dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2"
                                                            href="javascript:void(0)">
                                                            <iconify-icon class="text-xl"
                                                                icon="humbleicons:duplicate"></iconify-icon>
                                                            Duplicate
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="delete-button dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2"
                                                            href="javascript:void(0)">
                                                            <iconify-icon class="text-xl"
                                                                icon="mingcute:delete-2-line"></iconify-icon>
                                                            Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="connectedSortable ps-24 pt-24 pe-24 ui-sortable">
                                        <div class="kanban-card bg-neutral-50 p-16 radius-8 mb-24 ui-sortable-handle">
                                            No Call Statuses found for this Workflow.
                                        </div>
                                    </div>
                                    <!-- Add Task Button -->
                                    <button type="button"
                                        class="d-flex align-items-center gap-2 fw-medium w-100 text-primary-600 justify-content-center text-hover-primary-800 add-task-button">
                                        <iconify-icon icon="ph:plus-circle" class="icon text-xl"></iconify-icon>
                                        Add Task
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach ($workflow->callStatus as $index => $status)
                            <div class="kanban-item radius-12 {{ $classes['default'] }}"
                                data-status-id="{{ $status->id }}" data-workflow-id="{{ $workflow->id }}"
                                style="min-width: 250px;">
                                <div class="card p-0 radius-12">
                                    <div class="card-body p-0 pb-24">
                                        <div
                                            class="d-flex align-items-center gap-2 justify-content-between ps-24 pt-24 pe-24 card-header">
                                            <h6 class="text-lg fw-semibold mb-0">{{ $workflow->name }}</h6>
                                            <div class="d-flex align-items-center gap-3 justify-content-between mb-0">
                                                <button type="button"
                                                    class="text-2xl hover-text-primary add-task-button">
                                                    <iconify-icon icon="ph:plus-circle" class="icon"></iconify-icon>
                                                </button>
                                                <div class="dropdown">
                                                    <button type="button" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <iconify-icon icon="entypo:dots-three-vertical"
                                                            class="text-xl"></iconify-icon>
                                                    </button>
                                                    <ul class="dropdown-menu p-12 border bg-base shadow">
                                                        <li>
                                                            <a class="duplicate-button dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2"
                                                                href="javascript:void(0)">
                                                                <iconify-icon class="text-xl"
                                                                    icon="humbleicons:duplicate"></iconify-icon>
                                                                Duplicate
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="delete-button dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2"
                                                                href="javascript:void(0)">
                                                                <iconify-icon class="text-xl"
                                                                    icon="mingcute:delete-2-line"></iconify-icon>
                                                                Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="connectedSortable ps-24 pt-24 pe-24"
                                            id="sortable-{{ $workflow->id }}-{{ $status->id }}"
                                            data-workflow-id="{{ $workflow->id }}">
                                            @forelse ($calls->where('call_status_id', $status->id) as $call)
                                                <div class="kanban-card bg-neutral-50 p-16 radius-8 mb-24"
                                                    id="kanban-{{ $call->id }}"
                                                    data-current-status-id="{{ $call->call_status_id }}"
                                                    data-workflow-id="{{ $workflow->id }}">
                                                    <p class="kanban-desc text-secondary-light">
                                                        Assigned to:
                                                        <a href="{{ route('calls.edit', $call->id) }}"
                                                            class="text-lg text-secondary-light fw-semibold flex-grow-1">
                                                            {{ $call->relatedTo->name }}
                                                        </a>
                                                    </p>
                                                    <p class="kanban-desc text-secondary-light">
                                                        Assigned to type:
                                                        {{ str(RelatedToType::from($call->related_to_type)->name)->lower() }}
                                                    </p>
                                                    <h6 class="text-lg fw-semibold mb-8">Subject: {{ $call->subject }}
                                                    </h6>
                                                    <span class="start-date text-secondary-light">Time:
                                                        {{ $call->call_time }}</span>
                                                    <p class="kanban-desc text-secondary-light">Duration:
                                                        {{ $call->duration_in_minutes }} minutes</p>
                                                    <p class="kanban-desc text-secondary-light">{{ $call->notes }}</p>
                                                    <div
                                                        class="mt-12 d-flex align-items-center justify-content-between gap-10">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between gap-10">
                                                            <a href="{{ route('calls.edit', $call->id) }}"
                                                                class="btn btn-outline-primary btn-sm radius-8"
                                                                title="Edit Call">
                                                                <iconify-icon icon="lucide:edit"
                                                                    class="text-lg"></iconify-icon>
                                                            </a>
                                                            <form action="{{ route('calls.destroy', $call->id) }}"
                                                                method="POST" class="delete-call-form"
                                                                data-call-id="{{ $call->id }}">
                                                                @method('DELETE')
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-outline-danger btn-sm radius-8 card-delete-button"
                                                                    title="Delete Call">
                                                                    <iconify-icon icon="fluent:delete-24-regular"
                                                                        class="text-lg"></iconify-icon>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-secondary-light text-center">No calls in this status.
                                                </p>
                                            @endforelse
                                        </div>
                                        <!-- Add Task Button -->
                                        <button type="button"
                                            class="d-flex align-items-center gap-2 fw-medium w-100 text-primary-600 justify-content-center text-hover-primary-800 add-task-button">
                                            <iconify-icon icon="ph:plus-circle" class="icon text-xl"></iconify-icon>
                                            Add Task
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info w-100">
                No Workflows found.
            </div>
        @endforelse
    </div>
</div>
