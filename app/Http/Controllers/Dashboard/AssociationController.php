<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Association\AddMemberAssociationRequest;
use App\Http\Requests\Association\addPaymentAssociationRequest;
use App\Http\Requests\Association\StoreAssociationRequest;
use App\Http\Requests\Association\UpdateAssociationRequest;
use App\Http\Resources\AssociationResource;
use App\Models\Association;
use App\Models\AssociationPayment;
use App\Models\Client;
use App\Models\PaymentWay;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AssociationController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:associations_index')->only('index', 'list');
        $this->middleware('check.permission:associations_store')->only('store');
        $this->middleware('check.permission:associations_details')->only('details');
        $this->middleware('check.permission:associations_update')->only('update');
        $this->middleware('check.permission:associations_destroy')->only('destroy');
    }

    public function index()
    {
        $clients = Client::all();

        return view('dashboard.associations.index', compact('clients'));
    }

    public function list()
    {
        $associations = Association::with(['members.client', 'creator'])->get();

        return response()->json(['status' => true, 'message' => __('messages.associations_fetched_successfully'), 'data' => AssociationResource::collection($associations)]);
    }

    public function details($id)
    {
        $association = Association::with(['members.client', 'creator'])->findOrFail($id);
        $clients = Client::all();
        $paymentWays = PaymentWay::all();
        return view('dashboard.associations.details', compact('association', 'clients','paymentWays'));
    }

    public function addMember(AddMemberAssociationRequest $request, $id)
    {
        $data = $request->validated();
        $association = Association::findOrFail($id);
        $members = $association->members()->orderBy('payout_order')->get();
        $lastOrder = $members->max('payout_order') ?? 0;

        if ($members->isEmpty()) {
            $receiveDate = Carbon::parse($association->start_date);
        } else {
            $lastReceiveDate = Carbon::parse($members->last()->receive_date);
            $receiveDate = $lastReceiveDate->copy()->addDays((int) $association->per_day);
        }

        $member = $association->members()->create([
            'client_id' => $data['client_id'],
            'payout_order' => $lastOrder + 1,
            'receive_date' => $receiveDate,
        ]);

        $newTotal = $association->members()->count();

        $association->update([
            'total_members' => $newTotal,
            'end_date' => Carbon::parse($association->start_date)->addDays(($newTotal - 1) * (int) $association->per_day),
        ]);

        return response()->json(['status' => true, 'message' => __('messages.member_added_successfully'), 'data' => $member->load('client')]);
    }

    public function store(StoreAssociationRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $memberCount = count($data['total_members']);

        $endDate = Carbon::parse($data['start_date'])->addDays(($memberCount - 1) * $data['per_day']);

        $association = DB::transaction(function () use ($data, $endDate, $memberCount) {

            $association = Association::create([
                'name' => $data['name'],
                'per_day' => $data['per_day'],
                'monthly_amount' => $data['monthly_amount'],
                'start_date' => $data['start_date'],
                'end_date' => $endDate,
                'total_members' => $memberCount,
                'created_by' => $data['created_by'],
            ]);

            $startDate = Carbon::parse($data['start_date']);

            foreach ($data['total_members'] as $index => $clientId) {
                $receiveDate = $startDate->copy()->addDays($index * $data['per_day']);

                $association->members()->create([
                    'client_id' => is_array($clientId) ? $clientId[0] : $clientId,
                    'payout_order' => $index + 1,
                    'receive_date' => $receiveDate,
                ]);
            }

            return $association;
        });

        return response()->json(['status' => true, 'message' => __('messages.association_created_successfully'), 'data' => $association->load('members.client')], 201);
    }

    
    public function deleteMember($associationId, $memberId)
{
    $association = Association::findOrFail($associationId);
    $member = $association->members()->findOrFail($memberId);

    if ($member->payments()->exists()) {
        return response()->json(['status' => false,'message' => __('messages.cannot_delete_member_with_payments')], 400);
    }

    DB::transaction(function () use ($association, $member) {
        $member->delete();

        $members = $association->members()->orderBy('payout_order')->get();
        $startDate = Carbon::parse($association->start_date);
        $perDay = (int) $association->per_day;

        foreach ($members as $index => $m) {
            $newReceiveDate = $startDate->copy()->addDays($index * $perDay);
            $m->update([
                'payout_order' => $index + 1,
                'receive_date' => $newReceiveDate,
            ]);
        }

        $newTotal = $members->count();
        $association->update([
            'total_members' => $newTotal,
            'end_date' => $startDate->copy()->addDays(($newTotal - 1) * $perDay),
        ]);
    });

    return response()->json([
        'status' => true,
        'message' => __('messages.member_deleted_successfully')
    ]);
}

    public function addPayment(addPaymentAssociationRequest $request, $id)
    {
        $association = Association::findOrFail($id);
        $data = $request->validated();

        $member = $association->members()->findOrFail($data['member_id']);
        $totalInstallments = $association->total_members; 
        $monthlyAmount = $association->monthly_amount;
        $totalDue = $totalInstallments * $monthlyAmount;
        $totalPaid = $member->payments()->sum('amount');
        $newAmount = $data['amount'];
        if ($totalPaid + $newAmount > $totalDue) {
            return response()->json([    'status' => false,    'message' => __('messages.amount_exceeds_total', ['max' => $totalDue - $totalPaid]),], 400);
        }

        $data['association_id'] = $id;
        $data['created_by'] = Auth::id();

        $payment = AssociationPayment::create($data);

        return response()->json(['status' => true,'message' => __('messages.payment_added_successfully'),'data' => $payment,]);
    }

    public function receiveMoney(Request $request, $associationId)
    {
        $association = Association::findOrFail($associationId);

        $member = $association->members()->findOrFail($request['member_id']);

        if ($member->has_received) {
            return response()->json(['status' => false, 'message' => __('messages.this_member_recevied')], 400);
        }

        $member->update(['has_received' => true]);

        return response()->json(['status' => true,'message' => __('messages.recevied_done'),'data' => $member]);
    }

    public function show($id)
    {
        $association = Association::with(['members.client', 'payments', 'creator'])->findOrFail($id);

        return response()->json(['status' => true, 'message' => __('messages.association_fetched_successfully'), 'data' => new AssociationResource($association)]);
    }

    public function update(UpdateAssociationRequest $request, $id)
    {
        $association = Association::findOrFail($id);
        $association->update($request->validated());

        return response()->json(['status' => true, 'message' => __('messages.association_updated_successfully'), 'data' => new AssociationResource($association)]);
    }

    public function destroy($id)
    {
        $association = Association::findOrFail($id);

        if ($association->members()->exists() || $association->payments()->exists()) {
            return response()->json(['status' => false, 'message' => __('messages.cannot_delete_association_with_data')], 400);
        }

        $association->delete();

        return response()->json(['status' => true, 'message' => __('messages.association_deleted_successfully')]);
    }
}
