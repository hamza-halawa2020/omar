<?php

namespace App\Http\Controllers\Dashboard;

use App\DTO\ContactFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Contacts\StoreRequest;
use App\Models\Contact;
use App\Services\AccountServiceInterface;
use App\Services\ContactServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(
        private readonly ContactServiceInterface $contactService,
        private readonly AccountServiceInterface $accountService
    )
    {
    }

    public function index()
    {
        $contacts = $this->contactService->getAll(new ContactFilter(withAccount: true));

        return view('dashboard.crud.contacts.index', [
            'contacts' => $contacts,
            'title' => 'Contacts',
            'subTitle' => 'All contacts'
        ]);
    }

    public function create(Request $request)
    {
        $accountsSelect = $this->accountService->pluck('name', 'id');

        return view('dashboard.crud.contacts.create', [
            'accountsSelect' => $accountsSelect,
            'title' => 'Contacts',
            'subTitle' => 'Create contact',
            'queryParams' => $request->query()
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->contactService->create($request->validated());

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function edit(Contact $contact)
    {
        $accountsSelect = $this->accountService->pluck('name', 'id');

        return view('dashboard.crud.contacts.edit', [
            'accountsSelect' => $accountsSelect,
            'contact' => $contact,
            'title' => 'Contacts',
            'subTitle' => 'Edit contact'
        ]);
    }

    public function update(StoreRequest $request, Contact $contact): RedirectResponse
    {
        $this->contactService->update($contact, $request->validated());

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->contactService->delete($contact);

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }
}
