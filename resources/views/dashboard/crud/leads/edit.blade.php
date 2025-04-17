@php
    use App\Enums\Leads\FlagType;
    use App\Enums\Leads\SourceType;
    use App\Enums\Leads\StatusType;
@endphp

@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="text-lg font-weight-semibold mb-0">Edit lead</h6>
                        <form action="{{ route('leads.convert', $lead->id) }}" method="post">
                            @csrf
                            <x-forms.buttons.success>
                                Convert
                            </x-forms.buttons.success>
                        </form>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('leads.update', $lead) }}">
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
                                    Status
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="status" required>
                                    <option value="">Select status</option>
                                    @foreach(StatusType::cases() as $status)
                                        <option
                                            value="{{ $status->value }}"
                                            @selected(old('status', $lead->status) === $status->value)
                                        >
                                            {{ str($status->value)->replace('_', ' ')->lower()->ucfirst() }}
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
@endsection
