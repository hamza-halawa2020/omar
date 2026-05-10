<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Association\AddMemberAssociationRequest;
use App\Http\Requests\Association\addPaymentAssociationRequest;
use App\Http\Requests\Association\payMemberAssociationRequest;
use App\Http\Requests\Association\StoreAssociationRequest;
use App\Http\Requests\Association\UpdateAssociationRequest;
use App\Http\Resources\AssociationResource;
use App\Services\AssociationService;
use Illuminate\Routing\Controller as BaseController;

class AssociationController extends BaseController
{
    public function __construct(private readonly AssociationService $associationService)
    {
        $this->middleware('check.permission:associations_index')->only('index', 'list');
        $this->middleware('check.permission:associations_store')->only('store');
        $this->middleware('check.permission:associations_details')->only('details');
        $this->middleware('check.permission:associations_update')->only('update');
        $this->middleware('check.permission:associations_destroy')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.associations.index', $this->associationService->indexData());
    }

    public function list()
    {
        $associations = $this->associationService->list();

        return response()->json(['status' => true, 'message' => __('messages.associations_fetched_successfully'), 'data' => AssociationResource::collection($associations)]);
    }

    public function details($id)
    {
        return view('dashboard.associations.details', $this->associationService->details((int) $id));
    }

    public function addMember(AddMemberAssociationRequest $request, $id)
    {
        $member = $this->associationService->addMember((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.member_added_successfully'), 'data' => $member]);
    }

    public function store(StoreAssociationRequest $request)
    {
        $association = $this->associationService->store($request->validated());

        return response()->json(['status' => true, 'message' => __('messages.association_created_successfully'), 'data' => $association], 201);
    }

    public function deleteMember($associationId, $memberId)
    {
        $this->associationService->deleteMember((int) $associationId, (int) $memberId);

        return response()->json(['status' => true,'message' => __('messages.member_deleted_successfully')]);
    }

    public function addPayment(addPaymentAssociationRequest $request, $id)
    {
        $result = $this->associationService->addPayment((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.payment_added_successfully'), 'data' => $result]);
    }

    public function payMember(payMemberAssociationRequest $request, $id)
    {
        $result = $this->associationService->payMember((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.recevied_done'), 'data' => $result]);
    }

    public function show($id)
    {
        $association = $this->associationService->show((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.association_fetched_successfully'), 'data' => new AssociationResource($association)]);
    }

    public function update(UpdateAssociationRequest $request, $id)
    {
        $association = $this->associationService->update((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.association_updated_successfully'), 'data' => new AssociationResource($association)]);
    }

    public function destroy($id)
    {
        $this->associationService->destroy((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.association_deleted_successfully')]);
    }
}
