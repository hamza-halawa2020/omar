@php use App\Enums\Deals\StageType;use App\Enums\Tasks\RelatedToType;use App\Enums\Tasks\Status; @endphp
@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Create deal</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('deals.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input-label.basic name="name" required value="{{ old('name') }}">
                                    Name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="amount"
                                    required value="{{ old('amount') }}"
                                    type="number">
                                    Amount
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Account
                                </x-forms.labels.basic>
                                <x-forms.select.basic
                                    name="account_id"
                                    id="accountsSelect"
                                    required
                                >
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

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Contact
                                </x-forms.labels.basic>
                                <x-forms.select.basic
                                    name="contact_id"
                                    id="contactsSelect"
                                    required
                                >
                                    <option value="">Select contact</option>
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Stage
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="stage" required>
                                    <option value="">Select stage</option>
                                    @foreach(StageType::cases() as $stage)
                                        <option
                                            value="{{ $stage->value }}"
                                            @selected(old('stage') === $stage->value)
                                        >
                                            {{ str($stage->value)->replace('_', '')->ucfirst() }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-md-6">
                                <x-forms.input-label.basic
                                    name="expected_close_date"
                                    required value="{{ old('expected_close_date') }}"
                                    type="date">
                                    Expected close date
                                </x-forms.input-label.basic>
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

    <script>
        const accountsSelect = document.getElementById('accountsSelect');
        const contactsSelect = document.getElementById('contactsSelect');

        // Get old values from the server
        const oldAccountId = "{{ old('account_id') }}";
        const oldContactId = "{{ old('contact_id') }}";

        async function getAccountContacts(accountId, selectedContactId = null) {
            const url = `{{ route('api.v1.contacts.list') }}?account_id=${accountId}`;

            try {
                const res = await fetch(url);
                const data = await res.json();

                contactsSelect.innerHTML = '';

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select contact';
                defaultOption.disabled = true;
                contactsSelect.appendChild(defaultOption);

                Object.entries(data).forEach(([id, name]) => {
                    const option = document.createElement('option');
                    option.value = id;
                    option.textContent = name;
                    if (selectedContactId && selectedContactId == id) {
                        option.selected = true;
                        defaultOption.selected = false;
                    }
                    contactsSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Failed to fetch contacts:', error);
            }
        }

        // On change of account dropdown
        accountsSelect.addEventListener('change', (e) => {
            const accountId = e.target.value;
            if (accountId) {
                getAccountContacts(accountId);
            }
        });

        // On page load: load contacts if old account_id exists
        document.addEventListener('DOMContentLoaded', () => {
            if (oldAccountId) {
                getAccountContacts(oldAccountId, oldContactId);
            }
        });
    </script>

@endsection
