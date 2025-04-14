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
                                <x-forms.input-label.basic name="name" required>
                                    Name
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
                                <x-forms.labels.basic>
                                    Source
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="source" required>
                                    <option value="">Select source</option>
                                    <option value="website">Website</option>
                                    <option value="referral">Referral</option>
                                    <option value="ads">Ads</option>
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Status
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="status" required>
                                    <option value="">Select status</option>
                                    <option value="win">Win</option>
                                    <option value="lose">Lose</option>
                                    <option value="new_task">New task</option>
                                    <option value="no_answer">No Answer</option>
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Assigned to
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="assigned_to">
                                    <option value="">Select user</option>
                                    @foreach($usersSelect as $id => $fullName)
                                        <option value="{{ $id }}">{{ $fullName }}</option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Flag
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="flag">
                                    <option value="">Select flag</option>
                                    <option value="hot">Hot</option>
                                    <option value="normal">Normal</option>
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
