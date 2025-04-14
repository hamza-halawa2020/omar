@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="text-lg font-weight-semibold mb-0">Edit Lead</h6>
                        <form method="POST" action="{{ route('leads.convert', ['lead' => $lead->id]) }}">
                            @csrf
                            <x-forms.buttons.success-sm class="font-bold">
                                Convert
                            </x-forms.buttons.success-sm>
                        </form>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('leads.update', $lead->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input-label.basic name="name" required value="{{ old('name', $lead->name) }}">
                                    Name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="email" required value="{{ old('email', $lead->email) }}">
                                    Email
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="phone" required value="{{ old('phone', $lead->phone) }}">
                                    Phone
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="industry" required value="{{ old('industry', $lead->industry) }}">
                                    Industry
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="address" required value="{{ old('address', $lead->address) }}">
                                    Address
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="position" required value="{{ old('position', $lead->position) }}">
                                    Position
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>Source</x-forms.labels.basic>
                                <x-forms.select.basic name="source" required>
                                    <option value="">Select source</option>
                                    <option value="website" {{ old('source', $lead->source) == 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="referral" {{ old('source', $lead->source) == 'referral' ? 'selected' : '' }}>Referral</option>
                                    <option value="ads" {{ old('source', $lead->source) == 'ads' ? 'selected' : '' }}>Ads</option>
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>Status</x-forms.labels.basic>
                                <x-forms.select.basic name="status" required>
                                    <option value="">Select status</option>
                                    <option value="win" {{ old('status', $lead->status) == 'win' ? 'selected' : '' }}>Win</option>
                                    <option value="lose" {{ old('status', $lead->status) == 'lose' ? 'selected' : '' }}>Lose</option>
                                    <option value="new_task" {{ old('status', $lead->status) == 'new_task' ? 'selected' : '' }}>New task</option>
                                    <option value="no_answer" {{ old('status', $lead->status) == 'no_answer' ? 'selected' : '' }}>No Answer</option>
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>Assigned to</x-forms.labels.basic>
                                <x-forms.select.basic name="assigned_to">
                                    <option value="">Select user</option>
                                    @foreach($usersSelect as $id => $fullName)
                                        <option value="{{ $id }}" {{ $id == old('assigned_to', $lead->assigned_to) ? 'selected' : '' }}>
                                            {{ $fullName }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>Flag</x-forms.labels.basic>
                                <x-forms.select.basic name="flag">
                                    <option value="">Select flag</option>
                                    <option value="hot" {{ old('flag', $lead->flag) == 'hot' ? 'selected' : '' }}>Hot</option>
                                    <option value="normal" {{ old('flag', $lead->flag) == 'normal' ? 'selected' : '' }}>Normal</option>
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="last_follow_up"
                                    type="datetime-local"
                                    value="{{ old('last_follow_up', $lead->last_follow_up ? \Carbon\Carbon::parse($lead->last_follow_up)->format('Y-m-d\TH:i') : '') }}">
                                    Last follow up
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="next_follow_up"
                                    type="datetime-local"
                                    value="{{ old('next_follow_up', $lead->next_follow_up ? \Carbon\Carbon::parse($lead->next_follow_up)->format('Y-m-d\TH:i') : '') }}">
                                    Next follow up
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-12">
                                <x-forms.labels.basic>Notes</x-forms.labels.basic>
                                <x-forms.textarea.basic name="notes">{{ old('notes', $lead->notes) }}</x-forms.textarea.basic>
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
@endsection
