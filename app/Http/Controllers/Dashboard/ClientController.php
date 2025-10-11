<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\PaymentWay;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class ClientController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:clients_index')->only('index', 'list');
        $this->middleware('check.permission:clients_debts')->only('debts', 'listDebts');
        $this->middleware('check.permission:clients_installments')->only('client_installments', 'listClientInstallments');
        $this->middleware('check.permission:client_creditor')->only('client_creditor', 'listCreditor');
        $this->middleware('check.permission:clients_store')->only('store');
        $this->middleware('check.permission:clients_show')->only('show', 'showPage');
        $this->middleware('check.permission:clients_update')->only('update');
        $this->middleware('check.permission:clients_destroy')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.clients.index');
    }

    public function debts()
    {
        return view('dashboard.clients.debts');
    }

    public function client_installments()
    {
        return view('dashboard.clients.client_installments');
    }

    public function client_creditor()
    {
        return view('dashboard.clients.client_creditor');
    }

    public function list()
    {
        $clients = Client::with(['creator', 'transactions'])->orderByDesc('debt')->get();

        return response()->json(['status' => true, 'message' => __('messages.clients_fetched_successfully'), 'data' => ClientResource::collection($clients)]);
    }

    public function listDebts()
    {
        $clients = Client::where('debt', '>', 0)
            ->whereDoesntHave('installmentContracts')
            ->with(['creator', 'transactions', 'installmentContracts'])
            ->orderByDesc('debt')
            ->get();

        return response()->json(['status' => true, 'message' => __('messages.clients_fetched_successfully'), 'data' => ClientResource::collection($clients)]);
    }

    public function listCreditor()
    {
        $clients = Client::where('debt', '<', 0)
            ->with(['creator', 'transactions', 'installmentContracts'])
            ->orderBy('debt', 'asc')
            ->get();

        return response()->json(['status' => true, 'message' => __('messages.clients_fetched_successfully'), 'data' => ClientResource::collection($clients)]);
    }

    public function listClientInstallments()
    {
        $clients = Client::where('debt', '!=', 0)
            ->whereHas('installmentContracts')
            ->with(['creator', 'transactions', 'installmentContracts'])
            ->orderByDesc('debt')
            ->get();

        return response()->json(['status' => true, 'message' => __('messages.clients_fetched_successfully'), 'data' => ClientResource::collection($clients)]);
    }

    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        $client = Client::create($data);

        // event(new CreateBackup);

        return response()->json(['status' => true, 'message' => __('messages.client_created_successfully'), 'data' => new ClientResource($client)], 201);
    }

    public function show($id)
    {
        $client = Client::with(['creator', 'transactions'])->findOrFail($id);

        return response()->json(['status' => true, 'message' => __('messages.client_fetched_successfully'), 'data' => new ClientResource($client)]);
    }

    public function showPage($id)
    {
        $client = Client::with(['creator', 'transactions.paymentWay', 'installmentContracts.installments.payments'])->findOrFail($id);
        $paymentWays = PaymentWay::all();

        if (request()->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => __('messages.client_fetched_successfully'),
                'data' => new ClientResource($client),
            ]);
        }

        return view('dashboard.clients.show', [
            'client' => $client,
            'remaining_amount' => $client->total_remaining_amount,
            'remaining_installments' => $client->total_remaining_installments,
            'paymentWays' => $paymentWays,
        ]);

    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $data = $request->validated();

        if ($client->transactions()->exists() || $client->installmentContracts()->exists()) {

            return response()->json(['status' => false, 'message' => __('messages.cannot_update_client_with_transactions')], 400);
        }

        $client->update($data);

        // event(new CreateBackup);

        return response()->json(['status' => true, 'message' => __('messages.client_updated_successfully'), 'data' => new ClientResource($client)]);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        if ($client->transactions()->exists() || $client->installmentContracts()->exists()) {
            return response()->json(['status' => false, 'message' => __('messages.cannot_delete_client_with_transactions')], 400);
        }

        $client->delete();

        // event(new CreateBackup);

        return response()->json(['status' => true, 'message' => __('messages.client_deleted_successfully')]);
    }
}
