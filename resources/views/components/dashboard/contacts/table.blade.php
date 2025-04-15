<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 rounded-3 p-3">
                <div class="card-header bg-white py-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold text-dark">Contacts</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        @if($contacts->count() > 0)
                            <table class="table bordered-table mb-0 table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Position</th>
                                    <th scope="col" class="text-center">Account</th>
                                    <th scope="col" class="text-center">Created At</th>
                                    <th scope="col" class="text-center">Updated At</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($contacts as $contact)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                    <span
                                                        class="text-lg text-secondary-light fw-semibold flex-grow-1">{{ $contact->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ $contact->phone }}</td>
                                        <td>{{ $contact->position }}</td>
                                        <td>
                                            <a href="{{ route('accounts.edit', $contact->account_id) }}">
                                                {{ $contact->account->name }}
                                            </a>
                                        </td>
                                        <td class="text-center">{{ $contact->created_at ?? '-' }}</td>
                                        <td class="text-center">{{ $contact->updated_at ?? '-' }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('contacts.edit', $contact->id) }}"
                                                   class="btn btn-outline-primary btn-sm radius-8"
                                                   title="Edit Contact">
                                                    <iconify-icon icon="lucide:edit" class="text-lg"></iconify-icon>
                                                </a>
                                                <form
                                                    action="{{ route('contacts.destroy', ['contact' => $contact->id]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-outline-danger btn-sm radius-8"
                                                            title="Delete Contact">
                                                        <iconify-icon icon="fluent:delete-24-regular"
                                                                      class="text-lg"></iconify-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-light text-center rounded-3 shadow-sm mt-8" role="alert">
                                <i class="bi bi-info-circle me-2"></i> No contacts exist
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8 px-4">
        {{ $contacts->links() }}
    </div>
</div>
