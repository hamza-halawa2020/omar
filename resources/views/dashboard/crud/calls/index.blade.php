@php
    use App\Enums\Calls\RelatedToType;
@endphp
@extends('dashboard.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="text-lg fw-semibold mb-0">{{ $title }} - {{ $subTitle }}</h6>
                <div class="">
                    <button class="btn btn-outline-primary view-toggle-btn" data-view="kanban" style="display: none;">
                        Kanban View
                    </button>
                    <button class="btn btn-outline-primary view-toggle-btn" data-view="list">
                        List View
                    </button>
                </div>
            </div>
            <div class="card-body view-content">
                @include('dashboard.crud.calls.partials.kanban')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $(document).ready(function() {
            let currentView = 'kanban';

            initializeSortable();

            $('.view-toggle-btn').on('click', function(e) {
                e.preventDefault();
                const view = $(this).data('view');

                if (view === currentView) {
                    return;
                }

                const url = view === 'kanban' ? "{{ route('calls.kanban.partial') }}" :
                    "{{ route('calls.list.partial') }}";

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $('.view-content').html(response);

                        if (view === 'kanban') {
                            initializeSortable();
                        }

                        $('.view-toggle-btn').hide();
                        $(`.view-toggle-btn[data-view="${view === 'kanban' ? 'list' : 'kanban'}"]`)
                            .show();

                        currentView = view;
                    },
                    error: function(err) {
                        alert('Error loading view!');
                        console.error(err);
                    }
                });
            });

            function initializeSortable() {
                // تهيئة Sortable لكل WorkFlow
                $('[id^="sortable-wrapper-"]').each(function() {
                    $(this).sortable({
                        items: ".kanban-item",
                        handle: ".card-header",
                        update: function(event, ui) {
                            console.log('Columns reordered:', $(this).sortable("toArray"));
                        }
                    }).disableSelection();
                });

                // تهيئة Sortable للسحب والإفلات بين الأعمدة داخل نفس الـ WorkFlow
                $(".connectedSortable").sortable({
                    connectWith: ".connectedSortable[data-workflow-id]", // تحديد أن السحب يتم داخل نفس الـ WorkFlow فقط
                    update: function(event, ui) {
                        if (ui.sender) {
                            let callId = ui.item.attr('id').replace('kanban-', '');
                            let newStatusId = $(this).closest('.kanban-item').data('status-id');
                            let currentStatusId = ui.item.data('current-status-id');
                            let workflowId = $(this).closest('.kanban-item').data('workflow-id');
                            if (newStatusId && newStatusId !== currentStatusId) {
                                $.ajax({
                                    url: "{{ route('calls.update.call.status') }}",
                                    method: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        id: callId,
                                        call_status_id: newStatusId
                                    },
                                    success: function(res) {
                                        ui.item.data('current-status-id', newStatusId);
                                    },
                                    error: function(err) {
                                        alert('Error updating call status!');
                                        console.error(err);
                                    }
                                });
                            } else {
                                console.log("No change in call status, skipping update.");
                            }
                        }
                    }
                }).disableSelection();
            }
        });
    </script>
@endsection
