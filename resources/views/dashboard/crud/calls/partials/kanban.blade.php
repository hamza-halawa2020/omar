@php
    use App\Enums\Calls\RelatedToType;
    $outcomes = $calls->pluck('outcome')->unique()->values();

    $classes = [
        'completed' => 'completed-card',
        'no_answer' => 'no_answer-card',
        'rescheduled' => 'rescheduled-card',
    ];

    $columns = [];
    foreach ($outcomes as $index => $outcome) {
        $columns[] = [
            'title' => ucfirst(str_replace('_', ' ', $outcome)),
            'sortableId' => 'sortable' . ($index + 1),
            'calls' => $calls->where('outcome', $outcome),
            'class' => $classes[$outcome] ?? 'default-card',
        ];
    }
@endphp

<div class="overflow-x-auto scroll-sm pb-8">
    <div class="kanban-wrapper">
        <div class="d-flex align-items-start gap-24" id="sortable-wrapper">
            @foreach ($columns as $column)
                <div class="w-25 kanban-item radius-12 {{ $column['class'] }}"
                    data-outcome="{{ $column['calls']->first()->outcome ?? '' }}">
                    <div class="card p-0 radius-12">
                        <div class="card-body p-0 pb-24">
                            <div
                                class="d-flex align-items-center gap-2 justify-content-between ps-24 pt-24 pe-24 card-header">
                                <h6 class="text-lg fw-semibold mb-0">{{ $column['title'] }}</h6>
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
                            <div class="connectedSortable ps-24 pt-24 pe-24" id="{{ $column['sortableId'] }}">
                                @foreach ($column['calls'] as $call)
                                    <div class="kanban-card bg-neutral-50 p-16 radius-8 mb-24"
                                        id="kanban-{{ $call->id }}" data-current-outcome="{{ $call->outcome }}">
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
                                        <h6 class="kanban-title text-lg fw-semibold mb-8">Subject: {{ $call->subject }}
                                        </h6>
                                        <span class="start-date text-secondary-light">Time:
                                            {{ $call->call_time }}</span>
                                        <p class="kanban-desc text-secondary-light">Duration:
                                            {{ $call->duration_in_minutes }} minutes</p>
                                        <p class="kanban-desc text-secondary-light">{{ $call->notes }}</p>
                                        <div class="mt-12 d-flex align-items-center justify-content-between gap-10">
                                            <div class="d-flex align-items-center justify-content-between gap-10">
                                                <a href="{{ route('calls.edit', $call->id) }}"
                                                    class="btn btn-outline-primary btn-sm radius-8" title="Edit Call">
                                                    <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                                </a>
                                                <form action="{{ route('calls.destroy', $call->id) }}" method="POST"
                                                    class="delete-call-form" data-call-id="{{ $call->id }}">
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
                                @endforeach
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
            <div class="w-25 kanban-item radius-12 overflow-hidden">
                <div class="card p-0 radius-12 overflow-hidden shadow-none">
                    <div class="card-body p-24">
                        <a href="{{ route('calls.create') }}"
                            class="add-kanban d-flex align-items-center gap-2 fw-medium w-100 text-primary-600 justify-content-center text-hover-primary-800 line-height-1">
                            <i class="bi bi-plus-circle"></i> Add New Call
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
