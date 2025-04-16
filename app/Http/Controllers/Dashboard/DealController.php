<?php

namespace App\Http\Controllers\Dashboard;

use App\DTO\DealFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Crud\Deals\StoreRequest;
use App\Models\Deal;
use App\Services\AccountServiceInterface;
use App\Services\DealServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

class DealController extends Controller
{
    public function __construct(
        private readonly DealServiceInterface    $dealService,
        private readonly AccountServiceInterface $accountService,
    )
    {
    }

    public function index()
    {
        $deal = $this->dealService->getAll(new DealFilter(withContact: true, withAccount: true));
        return view('dashboard.crud.deals.index', [
            'deals' => $deal,
            'title' => 'Deals',
            'subTitle' => 'All Deals',
        ]);
    }

    public function create(Request $request, Deal $deal)
    {
        $accountsSelect = $this->accountService->pluck('name', 'id');

        return view('dashboard.crud.deals.create', [
            'deal' => $deal,
            'accountsSelect' => $accountsSelect,
            'title' => 'Deals',
            'subTitle' => 'Create Deal',
            'queryParams' => $request->query(),
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->dealService->create($request->validated());

        return redirect()->route('deals.index')->with('success', 'Deal has been created.');
    }

    public function edit(Deal $deal)
    {
        $accountsSelect = $this->accountService->pluck('name', 'id')->toArray();

        return view('dashboard.crud.deals.edit', [
            'deal' => $deal,
            'accountsSelect' => $accountsSelect,
            'title' => 'Deals',
            'subTitle' => 'Edit Deal',
        ]);
    }

    public function update(StoreRequest $request, Deal $deal)
    {
        $this->dealService->update($deal, $request->validated());

        return redirect()->route('deals.index')->with('success', 'Deal has been updated.');
    }

    public function destroy(Deal $deal)
    {
        $this->dealService->delete($deal);

        return redirect()->route('deals.index')->with('success', 'Deal has been deleted.');
    }
}
