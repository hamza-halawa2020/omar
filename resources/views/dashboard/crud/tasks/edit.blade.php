@php use App\Enums\Tasks\RelatedToType;use App\Enums\Tasks\Status; @endphp
@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Edit Task</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('tasks.update', $task) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input-label.basic name="title" required value="{{ old('title', $task->title) }}">
                                    Title
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="due_date" required value="{{ old('due_date', $task->due_date) }}"
                                                           type="date">
                                    Due date
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Status
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="status" required>
                                    <option value="">Select status</option>
                                    @foreach(Status::cases() as $status)
                                        <option
                                            value="{{ $status->value }}"
                                            @selected(old('status', $task->status) === $status->value)
                                        >
                                            {{ str($status->value)->replace('_', '')->ucfirst() }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Assigned to
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="assigned_to" required>
                                    <option value="">Select user</option>
                                    @foreach($usersSelect as $id => $fullName)
                                        <option
                                            value="{{ $id }}"
                                            @selected(old('assigned_to', $task->assigned_to) == $id)
                                        >
                                            {{ $fullName }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Related to type
                                </x-forms.labels.basic>
                                <x-forms.select.basic id="relatedToTypeSelect" name="related_to_type" required>
                                    <option value="">Select type</option>
                                    @foreach(RelatedToType::cases() as $relatedToType)
                                        <option
                                            @selected(old('related_to_type', $task->related_to_type) == $relatedToType->value)
                                            value="{{ strtolower($relatedToType->name) }}"
                                        >
                                            {{ strtolower($relatedToType->name) }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Related to
                                </x-forms.labels.basic>
                                <x-forms.select.basic
                                    name="related_to_id"
                                    id="relatedToSelect"
                                    required
                                >
                                    <option value="">Select</option>
                                </x-forms.select.basic>
                            </div>

                            <div class="col-12">
                                <x-forms.labels.basic>
                                    Notes
                                </x-forms.labels.basic>
                                <x-forms.textarea.basic name="notes">{{ old('notes', $task->notes) }}</x-forms.textarea.basic>
                            </div>

                            <div class="col-12 pt-4 text-end">
                                <x-forms.buttons.primary class="float-end">
                                    Update
                                </x-forms.buttons.primary>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const relatedToTypeSelect = document.getElementById('relatedToTypeSelect');
        const relatedToSelect = document.getElementById('relatedToSelect');

        const apiRoutes = {
            lead: @json(route('api.v1.leads.list')),
            contact: @json(route('api.v1.contacts.list')),
            deal: @json(route('api.v1.deals.list')),
        };

        const oldRelatedToType = "{{ old('related_to_type', strtolower(RelatedToType::from($task->related_to_type)->name)) }}";
        const oldRelatedToId = "{{ old('related_to_id', $task->related_to_id) }}";

        function getRelatedToData(relatedTo) {
            const apis = {
                'lead': async () => {
                    const res = await fetch(apiRoutes.lead);
                    return await res.json();
                },
                'contact': async () => {
                    const res = await fetch(apiRoutes.contact);
                    return await res.json();
                },
                'deal': async () => {
                    const res = await fetch(apiRoutes.deal);
                    return await res.json();
                }
            };

            return apis[relatedTo];
        }

        async function loadRelatedToOptions(type, selectedId = null) {
            const getData = getRelatedToData(type);

            if (!getData) {
                console.warn('No API handler for:', type);
                return;
            }

            const data = await getData();

            // Clear previous options
            relatedToSelect.innerHTML = '';

            // Add a placeholder option
            const placeholder = document.createElement('option');
            placeholder.textContent = 'Select';
            placeholder.disabled = true;
            placeholder.selected = !selectedId;
            relatedToSelect.appendChild(placeholder);

            // Populate options
            Object.entries(data).forEach(([id, name]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = name;
                if (selectedId && id === selectedId) {
                    option.selected = true;
                }
                relatedToSelect.appendChild(option);
            });
        }

        relatedToTypeSelect.addEventListener('change', e => {
            loadRelatedToOptions(e.target.value);
        });

        // Load related_to_id options on page load if there's an old related_to_type
        if (oldRelatedToType) {
            loadRelatedToOptions(oldRelatedToType, oldRelatedToId);
        }
    </script>
@endsection
