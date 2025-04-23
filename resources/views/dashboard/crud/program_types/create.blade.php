@php use App\Enums\Calls\OutcomeType;use App\Enums\Calls\RelatedToType; @endphp
@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Create call</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('calls.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Related to type
                                </x-forms.labels.basic>
                                <x-forms.select.basic
                                    name="related_to_type"
                                    id="relatedToTypeSelect"
                                    value="{{ old('related_to_type') }}"
                                    required
                                >
                                    <option value="">Select type</option>
                                    @foreach(RelatedToType::cases() as $type)
                                        <option
                                            value="{{ $type->value }}"
                                            @selected(old('related_to_type') === $type->value)
                                        >
                                            {{ str($type->name)->lower()->replace('_', ' ')->ucfirst() }}</option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Related to
                                </x-forms.labels.basic>
                                <x-forms.select.basic
                                    id="relatedToSelect"
                                    name="related_to_id"
                                    value="{{ old('related_to_id') }}"
                                    required
                                >
                                    <option value="">Select</option>
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="subject"
                                    value="{{ old('subject') }}"
                                    required
                                >
                                    subject
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="call_time"
                                    value="{{ old('call_time') }}"
                                    type="datetime-local"
                                    required
                                >
                                    Call time
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="duration_in_minutes"
                                    value="{{ old('duration_in_minutes') }}"
                                    type="number"
                                    required
                                >
                                    Duration in minutes
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Outcome
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="outcome" required>
                                    <option value="">Select outcome</option>
                                    @foreach(OutcomeType::cases() as $outcome)
                                        <option
                                            value="{{ $outcome->value }}"
                                            @selected(old('outcome') === $outcome->value)
                                        >
                                            {{ str($outcome->value)->replace('_', ' ')->lower()->ucfirst() }}</option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-12">
                                <x-forms.labels.basic>
                                    Notes
                                </x-forms.labels.basic>
                                <x-forms.textarea.basic
                                    name="notes"
                                >{{ old('notes') }}</x-forms.textarea.basic>
                            </div>

                            <div class="col-12 pt-4 text-end">
                                <x-forms.buttons.primary class="float-end">
                                    Submit
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

        const oldRelatedToType = "{{ old('related_to_type') }}";
        const oldRelatedToId = "{{ old('related_to_id') }}";

        function getRelatedToData(relatedTo) {
            const map = {
                'App\\Models\\Lead': async () => {
                    const res = await fetch(apiRoutes.lead);
                    return await res.json();
                },
                'App\\Models\\Contact': async () => {
                    const res = await fetch(apiRoutes.contact);
                    return await res.json();
                },
                'App\\Models\\Deal': async () => {
                    const res = await fetch(apiRoutes.deal);
                    return await res.json();
                }
            };

            return map[relatedTo];
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
                if (selectedId && id == selectedId) {
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
