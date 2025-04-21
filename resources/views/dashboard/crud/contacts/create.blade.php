@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Create contact</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('contacts.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="first_name"
                                    required
                                    value="{{ old('first_name') }}"
                                >
                                    First name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="last_name"
                                    required
                                    value="{{ old('last_name') }}"
                                >
                                    Last name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="email"
                                    required
                                    value="{{ old('email') }}"
                                >
                                    Email
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="phone"
                                    required
                                    value="{{ old('phone') }}"
                                >
                                    Phone
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="position"
                                    required
                                    value="{{ old('position') }}"
                                >
                                    Position
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Account
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="account_id">
                                    <option value="">Select account</option>
                                    @foreach($accountsSelect as $id => $name)
                                        <option
                                            value="{{ $id }}"
                                            @selected(old('account_id', $queryParams['account_id'] ?? null) == $id)
                                        >
                                            {{ $name }}
                                        </option>
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
