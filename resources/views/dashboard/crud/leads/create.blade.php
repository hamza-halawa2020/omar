@php use App\Enums\Leads\FlagType;use App\Enums\Leads\SourceType;use App\Enums\Leads\StatusType;use App\Enums\Leads\TypeOfContact; @endphp
@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Create lead</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('leads.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input-label.basic name="first_name" required>
                                    First name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="last_name" required>
                                    Last name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="email" required>
                                    Email
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="phone" required>
                                    Phone
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="industry" required>
                                    Industry
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="address" required>
                                    Address
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="position" required>
                                    Position
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="ad_code" required>
                                    Ad code
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
                                            @selected(old('source') === $source->value)
                                        >
                                            {{ str($source->value)->replace('_', ' ')->lower()->ucfirst() }}</option>
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
                                            @selected(old('assigned_to') == $id)
                                        >
                                            {{ $fullName }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Creator
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="creator_id">
                                    <option value="">Select user</option>
                                    @foreach($usersSelect as $id => $fullName)
                                        <option
                                            value="{{ $id }}"
                                            @selected(old('creator_id') == $id)
                                        >
                                            {{ $fullName }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Country
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="country_id">
                                    <option value="">Select country</option>
                                    @foreach($countriesSelect as $id => $name)
                                        <option
                                            value="{{ $id }}"
                                            @selected(old('country_id') == $id)
                                        >
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Type of contact
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="type_of_contact">
                                    <option value="">Select type</option>
                                    @foreach(TypeOfContact::cases() as $type)
                                        <option
                                            value="{{ $type->value }}"
                                            @selected(old('type_of_contact') == $type->value)
                                        >
                                            {{ $type->value }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Program type
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="program_type_id">
                                    <option value="">Select type</option>
                                    @foreach($programTypesSelect as $id => $name)
                                        <option
                                            value="{{ $id }}"
                                        >
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="last_follow_up" type="datetime-local">
                                    Last follow up
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="next_follow_up" type="datetime-local">
                                    Next follow up
                                </x-forms.input-label.basic>
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
                                            @selected(old('flag') === $flag->value)
                                        >
                                            {{ str($flag->value)->replace('_', ' ')->lower()->ucfirst() }}</option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-12">
                                <x-forms.labels.basic>
                                    Notes
                                </x-forms.labels.basic>
                                <x-forms.textarea.basic name="notes"/>
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
@endsection
