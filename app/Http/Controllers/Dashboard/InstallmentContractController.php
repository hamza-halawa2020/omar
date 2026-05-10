<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\InstallmentContract\PayRequest;
use App\Http\Requests\InstallmentContract\StoreInstallmentContractRequest;
use App\Http\Requests\InstallmentContract\UpdateInstallmentContractRequest;
use App\Http\Resources\InstallmentContractResource;
use App\Services\InstallmentContractService;
use Illuminate\Routing\Controller as BaseController;

class InstallmentContractController extends BaseController
{
    public function __construct(private readonly InstallmentContractService $installmentContractService)
    {
        $this->middleware('check.permission:installments_index')->only('index', 'list');
        $this->middleware('check.permission:installments_store')->only('store');
        $this->middleware('check.permission:installments_show')->only('show', 'showPage');
        $this->middleware('check.permission:installments_update')->only('update');
        $this->middleware('check.permission:installments_pay')->only('pay');
        $this->middleware('check.permission:installments_destroy')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.installment_contracts.index', $this->installmentContractService->indexData());
    }

    public function list()
    {
        $installments = $this->installmentContractService->list();

        return response()->json(['status' => true, 'message' => __('messages.installments_fetched_successfully'), 'data' => InstallmentContractResource::collection($installments)]);
    }

    public function store(StoreInstallmentContractRequest $request)
    {
        $contract = $this->installmentContractService->store($request->validated());

        return response()->json(['status' => true, 'message' => __('messages.Installment_contract_created_successfully'), 'data' => $contract], 201);
    }

    public function show($id)
    {
        $contract = $this->installmentContractService->show((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.installment_contract_fetched_successfully'), 'data' => new InstallmentContractResource($contract)]);
    }

    public function showPage($id)
    {
        return view('dashboard.installment_contracts.show', $this->installmentContractService->showPage((int) $id));
    }

    public function update(UpdateInstallmentContractRequest $request, $id)
    {
        $contract = $this->installmentContractService->update((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.installment_contract_updated_successfully'), 'data' => new InstallmentContractResource($contract)]);
    }

    public function pay(PayRequest $request)
    {
        $result = $this->installmentContractService->pay($request->validated());

        return response()->json(['status' => true, 'message' => __('messages.installment_paid_successfully'), 'data' => $result]);
    }

    public function destroy($id)
    {
        $this->installmentContractService->destroy((int) $id);

        return response()->json(['status' => true,'message' => __('messages.installment_contract_deleted_successfully')]);
    }
}
