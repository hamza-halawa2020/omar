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

    <script>
        const statusesListUrl = '{{ route('api.v1.lead-statuses.list') }}';
        const statuesContainer = document.querySelector('.statuses-container');

        function createStatusButton(innerText, statusId) {
            const updateLeadUrl = '{{ route('api.v1.leads.update', $lead->id) }}';

            console.log(updateLeadUrl);

            const button = document.createElement('button');

            button.classList.add('status-btn', 'btn', 'btn-primary', 'd-flex', 'align-items-center', 'gap-2', 'px-6', 'py-2', 'rounded', 'fs-6');

            button.setAttribute('type', 'button');
            button.setAttribute('data-status-id', statusId);

            button.textContent = innerText;

            button.addEventListener('click', async () => {
                try {
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

                        document.querySelector('.current-lead-status').innerText =
                            data.newStatus?.name
                                .replace('_', ' ')
                                .toLowerCase()
                                .replace(/^\w/, c => c.toUpperCase()) || '—';

                        await renderStatusButtons(statusId);

                    } else {
                        console.error('Failed to update status');
                        alert('Status update failed.');
                    }
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

        renderStatusButtons({{ $lead->status_id }});

    </script>

@endsection
