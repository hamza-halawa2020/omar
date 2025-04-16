@php use App\Enums\Calls\OutcomeType; @endphp
@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Edit call</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('calls.update', $call->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Contact
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="contact_id"
                                                      value="{{ old('contact_id', $call->contact_id) }}" required>
                                    <option value="">Select contact</option>
                                    @foreach($contacts as $id => $name)
                                        <option
                                            value="{{ $id }}"
                                            @selected(old('contact_id', $call->contact_id) === $id)
                                        >
                                            {{ $name }}</option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="subject"
                                    value="{{ old('subject', $call->subject) }}"
                                    required
                                >
                                    Subject
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="call_time"
                                    value="{{ old('call_time', \Carbon\Carbon::parse($call->call_time)->format('Y-m-d\TH:i')) }}"
                                    type="datetime-local"
                                    required
                                >
                                    Call time
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="duration_in_minutes"
                                    value="{{ old('duration_in_minutes', $call->duration_in_minutes) }}"
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
                                            @selected(old('outcome', $call->outcome) === $outcome->value)
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
                                >{{ old('notes', $call->notes) }}</x-forms.textarea.basic>
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
