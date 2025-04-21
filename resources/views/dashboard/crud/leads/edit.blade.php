@php
    use App\Enums\Leads\FlagType;
    use App\Enums\Leads\SourceType;
    use App\Enums\Leads\StatusType;use Illuminate\Support\Facades\Blade;use Illuminate\Support\Js;
@endphp

@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card d-flex justify-content-between align-items-start p-24 card-body">
                    <div class="d-flex flex-column gap-20">
                        <div class="text-lg mb-0 d-flex gap-20 align-items-center">
                            <span class="fs-4">
                                Current state
                            </span>
                            <span class="current-lead-status bg-secondary p-2 radius-24 text-white p-8"
                                  style="font-size: 12px; font-weight: bolder">
                                {{ str($lead->status->name)->replace('_', ' ')->ucfirst() }}
                            </span>
                        </div>
                        <div class="d-flex gap-44 mt-6 flex-wrap">
                            <span class="fs-4">Transitions</span>
                            <div class="d-flex gap-6 statuses-container">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 mt-20">
                    <form
                        class="card-body"
                        method="POST"
                        action="{{ route('leads.update', $lead) }}"
                        id="updateLeadForm"
                    >
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input-label.basic name="name" required value="{{ old('name', $lead->name) }}">
                                    Name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="email" required
                                                           value="{{ old('email', $lead->email) }}">
                                    Email
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="phone" required
                                                           value="{{ old('phone', $lead->phone) }}">
                                    Phone
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="industry" required
                                                           value="{{ old('industry', $lead->industry) }}">
                                    Industry
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="address" required
                                                           value="{{ old('address', $lead->address) }}">
                                    Address
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="position" required
                                                           value="{{ old('position', $lead->position) }}">
                                    Position
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Source
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="source" required>
                                    <option value="">Select source</option>
                                    @foreach(SourceType::cases() as $source)
                                        <option
                                            value="{{ $source->value }}"
                                            @selected(old('source', $lead->source) === $source->value)
                                        >
                                            {{ str($source->value)->replace('_', ' ')->lower()->ucfirst() }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Assigned to
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="assigned_to">
                                    <option value="">Select user</option>
                                    @foreach($usersSelect as $id => $fullName)
                                        <option
                                            value="{{ $id }}"
                                            @selected(old('assigned_to', $lead->assigned_to) == $id)
                                        >
                                            {{ $fullName }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Flag
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="flag">
                                    <option value="">Select flag</option>
                                    @foreach(FlagType::cases() as $flag)
                                        <option
                                            value="{{ $flag->value }}"
                                            @selected(old('flag', $lead->flag) === $flag->value)
                                        >
                                            {{ str($flag->value)->replace('_', ' ')->lower()->ucfirst() }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="last_follow_up" type="datetime-local"
                                                           value="{{ old('last_follow_up', $lead->last_follow_up) }}">
                                    Last follow up
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="next_follow_up" type="datetime-local"
                                                           value="{{ old('next_follow_up', $lead->next_follow_up) }}">
                                    Next follow up
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-12">
                                <x-forms.labels.basic>
                                    Notes
                                </x-forms.labels.basic>
                                <x-forms.textarea.basic name="notes" value="{{ old('notes', $lead->notes) }}"/>
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

    <!-- Schedule a call Modal -->
    <div class="modal fade" id="noAnswerModal" tabindex="-1" aria-labelledby="noAnswerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="noAnswerModalForm" method="POST" action="{{ route('api.v1.tasks.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="noAnswerModalLabel">Schedule a Call</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div>
                            <x-forms.input-label.basic
                                name="due_date"
                                required
                                value="{{ old('due_date') }}"
                                type="time"
                            >
                                Schedule a call with this lead tomorrow at:
                            </x-forms.input-label.basic>
                        </div>

                        <input
                            type="hidden"
                            name="title"
                            id="title"
                            value="Call the lead {{ $lead->name }}"
                            class="form-control"
                        >
                        <input type="hidden" name="status" value="pending">
                        <input type="hidden" name="assigned_to" value="1">
                        <input type="hidden" name="related_to_type" value="App\Models\Lead">
                        <input type="hidden" name="related_to_id" value="{{ $lead->id }}">
                    </div>

                    <div class="modal-footer">
                        <x-forms.buttons.primary id="noAnswerModalSubmit">
                            Save
                        </x-forms.buttons.primary>

                        <x-forms.buttons.secondary type="button" data-bs-dismiss="modal">
                            Close
                        </x-forms.buttons.secondary>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- status reason Modal -->
    <div class="modal fade" id="dropdownModal" tabindex="-1" aria-labelledby="dropdownModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="dropdownModalForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="dropdownModalLabel">Not interested reason</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div>
                            <x-forms.select.basic
                                name="status_reason"
                                required
                            >
                                <option value="test1">test1</option>
                                <option value="test2">test2</option>
                                <option value="test3">test3</option>
                            </x-forms.select.basic>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <x-forms.buttons.primary id="dropdownModalSubmit">
                            Save
                        </x-forms.buttons.primary>

                        <x-forms.buttons.secondary type="button" data-bs-dismiss="modal">
                            Close
                        </x-forms.buttons.secondary>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const statusesListUrl = '{{ route('api.v1.lead-statuses.list') }}';
        const statuesContainer = document.querySelector('.statuses-container');

        function createStatusButton(innerText, statusId) {
            const button = document.createElement('button');

            button.classList.add('status-btn', 'btn', 'btn-primary', 'd-flex', 'align-items-center', 'gap-2', 'px-6', 'py-2', 'rounded', 'fs-6');

            button.setAttribute('type', 'button');
            button.setAttribute('data-status-id', statusId);
            button.setAttribute('data-status-name', innerText);

            button.textContent = innerText;

            button.addEventListener('click', async e => {
                try {
                    if (e.target.innerText === 'No answer') {
                        const modal = new bootstrap.Modal(document.getElementById('noAnswerModal'));
                        modal.show();
                        return;
                    }

                    if (e.target.innerText === 'Not interested') {
                        const modal = new bootstrap.Modal(document.getElementById('dropdownModal'));
                        modal.show();
                        return;
                    }

                    await updateStatus(statusId);

                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while updating status.');
                }
            });

            return button;
        }

        async function renderStatusButtons(parentStatusId) {
            const url = new URL(statusesListUrl);
            url.searchParams.append('parent_id', parentStatusId);

            const statuses = await fetch(url)
                .then(res => res.json())
                .catch(error => {
                    console.error('Failed to fetch statuses:', error);
                    return null;
                });

            // Clear old buttons before rendering new ones
            statuesContainer.innerHTML = '';

            if (statuses) {
                for (const statusId in statuses) {
                    statuesContainer.appendChild(
                        createStatusButton(
                            statuses[statusId]
                                .replaceAll('_', ' ')
                                .toLowerCase()
                                .replace(/^\w/, (c) => c.toUpperCase()),
                            statusId
                        )
                    );
                }
            }
        }

        async function updateStatus(statusId) {
            const updateLeadUrl = '{{ route('api.v1.leads.update', $lead->id) }}';

            const response = await fetch(updateLeadUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    status_id: statusId
                })
            });

            if (response.ok) {
                const data = await response.json();

                console.log(data);

                document.querySelector('.current-lead-status').innerText =
                    data.lead?.status?.name
                        .replace('_', ' ')
                        .toLowerCase()
                        .replace(/^\w/, c => c.toUpperCase()) || '—';

                await renderStatusButtons(statusId);

            } else {
                console.error('Failed to update status');
                alert('Status update failed.');
            }
        }

        async function handleNoAnswerStatus() {
            const form = document.getElementById('noAnswerModalForm');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);

                formData.set('due_date', tomorrowAt(formData.get('due_date')));

                const response = await fetch("{{ route('api.v1.tasks.store') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (response.ok) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('noAnswerModal'));
                    modal.hide();

                    form.reset();
                }

                const noAnswerId = document.querySelector('.status-btn[data-status-name="No answer"]').getAttribute('data-status-id');

                await updateStatus(noAnswerId);
            });
        }

        async function handleNotInterestedStatus() {
            const form = document.getElementById('dropdownModalForm');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(form);
                const statusReason = formData.get('status_reason');

                const response = await fetch("{{ route('api.v1.leads.update', $lead->id) }}", {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({
                        status_reason: statusReason
                    }),
                });

                if (response.ok) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('dropdownModal'));
                    modal.hide();

                    form.reset();
                }

                const noAnswerId = document.querySelector('.status-btn[data-status-name="Not interested"]').getAttribute('data-status-id');

                await updateStatus(noAnswerId);
            });
        }

        function tomorrowAt(time) {
            if(!time)
                return;

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);

            const [hours, minutes] = time.split(':');

            // Set time to tomorrow's date
            tomorrow.setHours(hours);
            tomorrow.setMinutes(minutes);
            tomorrow.setSeconds(0);
            tomorrow.setMilliseconds(0);

            return tomorrow.toISOString().slice(0, 19).replace('T', ' ');
        }

        document.addEventListener('DOMContentLoaded', () => {
            handleNoAnswerStatus();
            handleNotInterestedStatus();
        });

        renderStatusButtons({{ $lead->status_id }});

    </script>
@endsection
