@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Edit Account</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('accounts.update', $account->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input-label.basic name="name" required
                                                           value="{{ old('name', $account->name) }}">
                                    Name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="industry" required
                                                           value="{{ old('industry', $account->industry) }}">
                                    Industry
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="email" required
                                                           value="{{ old('email', $account->email) }}">
                                    Email
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="phone" required
                                                           value="{{ old('phone', $account->phone) }}">
                                    Phone
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic name="address" required
                                                           value="{{ old('address', $account->address) }}">
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
                                        <option
                                            value="{{ $id }}" {{ $id == old('assigned_to', $account->assigned_to) ? 'selected' : '' }}>
                                            {{ $fullName }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
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
