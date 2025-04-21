@php use App\Enums\Tasks\RelatedToType; @endphp
@extends('dashboard.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-3 p-3">
                    <div class="card-header py-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">Tasks</h5>
                        <a href="{{ route('tasks.create') }}"
                           class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-plus-circle"></i> Add New Task
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            @if($tasks->count() > 0)
                                <table class="table bordered-table mb-0 table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Due date</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Related to type</th>
                                        <th scope="col" class="text-center">Related to</th>
                                        <th scope="col" class="text-center">Assigned To</th>
                                        <th scope="col" class="text-center">Notes</th>
                                        <th scope="col" class="text-center">Created At</th>
                                        <th scope="col" class="text-center">Updated At</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($tasks as $task)
                                        <tr>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                            class="text-lg text-secondary-light fw-semibold flex-grow-1">{{ $task->title }}</span>
                                                </div>
                                            </td>
                                            <td>{{ \Illuminate\Support\Carbon::create($task->due_date)->format('M d, Y h:i A') }}</td>
                                            <td>{{ $task->status }}</td>
                                            <td>{{ str(RelatedToType::from($task->related_to_type)->name)->lower() }}</td>
                                            <td class="text-center">{{ $task->relatedTo->name }}</td>
                                            <td class="text-center">{{ $task->assignedUser->full_name }}</td>
                                            <td class="text-center">{{ str($task->notes)->limit(30) }}</td>
                                            <td class="text-center">{{ $task->created_at ?? '-' }}</td>
                                            <td class="text-center">{{ $task->updated_at ?? '-' }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                                       class="btn btn-outline-primary btn-sm radius-8"
                                                       title="Edit Task">
                                                        <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                                    </a>
                                                    <form
                                                            action="{{ route('tasks.destroy', ['task' => $task->id]) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit"
                                                                class="btn btn-outline-danger btn-sm radius-8"
                                                                title="Delete Task">
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
                                    <i class="bi bi-info-circle me-2"></i> No tasks exist
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-8 px-4">
            {{ $tasks->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
