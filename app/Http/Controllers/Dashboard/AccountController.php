<?php

namespace App\Http\Controllers\Dashboard;

use App\DTO\AccountFilter;
use App\DTO\ContactFilter;
use App\DTO\DealFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Accounts\StoreRequest;
use App\Models\Account;
use App\Models\User;
use App\Services\AccountServiceInterface;
use App\Services\ContactServiceInterface;
use App\Services\DealServiceInterface;
use App\Services\impl\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(
        private readonly AccountServiceInterface $accountService,
        private readonly ContactServiceInterface $contactService,
        private readonly DealServiceInterface $dealService,
    )
    {
    }

    public function index()
    {
        $accounts = $this->accountService->getAll(new AccountFilter(withAssignedUser: true));

        return view('dashboard.crud.accounts.index', [
            'accounts' => $accounts,
            'title' => 'Accounts',
            'subTitle' => 'All accounts'
        ]);
    }

    public function create()
    {
        $usersSelect = User::select(['id', 'full_name'])->get()->pluck('full_name', 'id');

        return view('dashboard.crud.accounts.create', [
            'usersSelect' => $usersSelect,
            'title' => 'Accounts',
            'subTitle' => 'Create account'
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->accountService->create($request->validated());

        return redirect()->route('accounts.index')->with('success', 'Account created successfully');
    }

    public function edit(Account $account)
    {
        $usersSelect = User::select(['id', 'full_name'])->get()->pluck('full_name', 'id');

        $contacts = $this->contactService->getAll(new ContactFilter(accountId: $account->id, perPage: 5));

        $deals = $this->dealService->getAll(new DealFilter(accountId: $account->id, perPage: 5));

        return view('dashboard.crud.accounts.edit', [
            'usersSelect' => $usersSelect,
            'account' => $account,
            'contacts' => $contacts,
            'deals' => $deals,
            'title' => 'Accounts',
            'subTitle' => 'Edit account'
        ]);
    }

    public function update(StoreRequest $request, Account $account): RedirectResponse
    {
        $this->accountService->update($account, $request->validated());

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Account updated successfully');
    }

    public function destroy(Account $account)
    {
        $this->accountService->delete($account);

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully');
    }
}
