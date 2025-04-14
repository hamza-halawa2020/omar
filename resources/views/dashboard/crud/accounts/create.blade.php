@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Create account</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('accounts.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input-label.basic name="name" required value="{{ old('name') }}">
                                    Name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="industry" required value="{{ old('industry') }}">
                                    Industry
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="email" required value="{{ old('email') }}">
                                    Email
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="phone" required value="{{ old('phone') }}">
                                    Phone
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="address" required value="{{ old('address') }}">
                                    Address
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Assigned to
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="assigned_to">
                                    <option value="">Select user</option>
                                    @foreach($usersSelect as $id => $fullName)
                                        <option @selected(old('assigned_to') === $id) value="{{ $id }}">{{ $fullName }}</option>
                                    @endforeach
                                </x-forms.select.basic>
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
