<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use libphonenumber\PhoneNumberUtil;

class ClientController extends BaseController
{
    public function __construct(private readonly ClientService $clientService)
    {
        $this->middleware('check.permission:clients_index')->only('index', 'list');
        $this->middleware('check.permission:clients_debts')->only('debts', 'listDebts');
        $this->middleware('check.permission:clients_merchants')->only('merchants', 'listMerchants');
        $this->middleware('check.permission:clients_installments')->only('client_installments', 'listClientInstallments');
        $this->middleware('check.permission:client_creditor')->only('client_creditor', 'listCreditor');
        $this->middleware('check.permission:clients_store')->only('store');
        $this->middleware('check.permission:clients_show')->only('show', 'showPage');
        $this->middleware('check.permission:clients_update')->only('update');
        $this->middleware('check.permission:clients_destroy')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.clients.index', [
            'countryCodeOptions' => $this->countryCodeOptions(),
        ]);
    }

    public function debts()
    {
        return view('dashboard.clients.debts', [
            'countryCodeOptions' => $this->countryCodeOptions(),
        ]);
    }

    public function merchants()
    {
        return view('dashboard.clients.merchants', [
            'countryCodeOptions' => $this->countryCodeOptions(),
        ]);
    }

    public function client_installments()
    {
        return view('dashboard.clients.client_installments', [
            'countryCodeOptions' => $this->countryCodeOptions(),
        ]);
    }

    public function client_creditor()
    {
        return view('dashboard.clients.client_creditor', [
            'countryCodeOptions' => $this->countryCodeOptions(),
        ]);
    }

    public function list(Request $request)
    {
        $clients = $this->clientService->list($request);

        return response()->json(['status' => true,'message' => __('messages.clients_fetched_successfully'),'data' => ClientResource::collection($clients)]);
    }

    public function listDebts()
    {
        $clients = $this->clientService->listDebts(request('search'));

        return response()->json(['status' => true, 'message' => __('messages.clients_fetched_successfully'), 'data' => ClientResource::collection($clients)]);
    }

    public function listMerchants()
    {
        $clients = $this->clientService->listMerchants(request('search'));

        return response()->json(['status' => true, 'message' => __('messages.clients_fetched_successfully'), 'data' => ClientResource::collection($clients)]);
    }

    public function listCreditor()
    {
        $clients = $this->clientService->listCreditor(request('search'));

        return response()->json(['status' => true, 'message' => __('messages.clients_fetched_successfully'), 'data' => ClientResource::collection($clients)]);
    }

    public function listClientInstallments()
    {
        $clients = $this->clientService->listClientInstallments(request('search'));

        return response()->json(['status' => true, 'message' => __('messages.clients_fetched_successfully'), 'data' => ClientResource::collection($clients)]);
    }

    public function store(StoreClientRequest $request)
    {
        $client = $this->clientService->store($request->validated());

        // event(new CreateBackup);

        return response()->json(['status' => true, 'message' => __('messages.client_created_successfully'), 'data' => new ClientResource($client)], 201);
    }

    public function show($id)
    {
        $client = $this->clientService->show((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.client_fetched_successfully'), 'data' => new ClientResource($client)]);
    }

    public function showPage($id)
    {
        $data = $this->clientService->showPage((int) $id);
        $client = $data['client'];

        if (request()->expectsJson()) {
            return response()->json(['status' => true, 'message' => __('messages.client_fetched_successfully'), 'data' => new ClientResource($client)]);
        }

        return view('dashboard.clients.show', $data);

    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = $this->clientService->update((int) $id, $request->validated());

        // event(new CreateBackup);

        return response()->json(['status' => true, 'message' => __('messages.client_updated_successfully'), 'data' => new ClientResource($client)]);
    }

    public function destroy($id)
    {
        $this->clientService->destroy((int) $id);

        // event(new CreateBackup);

        return response()->json(['status' => true, 'message' => __('messages.client_deleted_successfully')]);
    }

    private function countryCodeOptions(): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $options = [];

        $hiddenRegions = ['IL'];

        foreach ($phoneUtil->getSupportedRegions() as $region) {
            if (in_array($region, $hiddenRegions, true)) {
                continue;
            }

            $code = '+' . $phoneUtil->getCountryCodeForRegion($region);
            $options[] = [
                'region' => $region,
                'code' => $code,
                'label' => "{$code} ({$region})",
            ];
        }

        usort($options, fn (array $a, array $b) => [$a['code'], $a['region']] <=> [$b['code'], $b['region']]);

        return $options;
    }
}
