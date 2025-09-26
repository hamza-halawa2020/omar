<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\CreateBackup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct()
    {
        // $this->middleware('check.permission:clients_index')->only('index', 'list');
        // $this->middleware('check.permission:clients_create')->only('store');
        // $this->middleware('check.permission:clients_show')->only('show');
        // $this->middleware('check.permission:clients_update')->only('update');
        // $this->middleware('check.permission:clients_delete')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.clients.index');
    }

    public function list()
    {
        $clients = Client::with(['creator', 'transactions'])->latest()->get();

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
        $client = Client::with(['creator', 'transactions'])->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json([
                'status' => true,
                'message' => __('messages.client_fetched_successfully'),
                'data' => new ClientResource($client),
            ]);
        }

        return view('dashboard.clients.show', compact('client'));
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $data = $request->validated();

        if ($client->transactions()->exists()) {
            return response()->json(['status' => false, 'message' => __('messages.cannot_update_client_with_transactions')], 400);
        }

        $client->update($data);

        // event(new CreateBackup);

        return response()->json(['status' => true, 'message' => __('messages.client_updated_successfully'), 'data' => new ClientResource($client)]);
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);

        if ($client->transactions()->exists()) {
            return response()->json(['status' => false, 'message' => __('messages.cannot_delete_client_with_transactions')], 400);
        }

        $client->delete();

        // event(new CreateBackup);

        return response()->json(['status' => true, 'message' => __('messages.client_deleted_successfully')]);
    }
}
