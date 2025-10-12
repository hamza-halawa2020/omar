<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Association\StoreAssociationRequest;
use App\Http\Requests\Association\UpdateAssociationRequest;
use App\Http\Resources\AssociationResource;
use App\Models\Association;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssociationController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:associations_index')->only('index', 'list');
        $this->middleware('check.permission:associations_store')->only('store');
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

        return response()->json([
            'status' => true,
            'message' => __('messages.associations_fetched_successfully'),
            'data' => AssociationResource::collection($associations),
        ]);
    }

    public function store(StoreAssociationRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $memberCount = count($data['total_members']);

        $endDate = Carbon::parse($data['start_date'])->addMonths($memberCount - 1);

        $association = DB::transaction(function () use ($data, $endDate, $memberCount) {

            $association = Association::create([
                'name' => $data['name'],
                'monthly_amount' => $data['monthly_amount'],
                'start_date' => $data['start_date'],
                'end_date' => $endDate,
                'total_members' => $memberCount,
                'created_by' => $data['created_by'],
            ]);

            foreach ($data['total_members'] as $index => $clientId) {
                $association->members()->create([
                    // 'client_id' => $clientId,
                    'client_id' => is_array($clientId) ? $clientId[0] : $clientId,
                    'payout_order' => $index + 1,
                ]);
            }

            return $association;
        });

        return response()->json(['status' => true,'message' => __('messages.association_created_successfully'),'data' => $association->load('members.client')], 201);
    }

    public function show($id)
    {
        $association = Association::with(['members.client', 'payments', 'creator'])
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => __('messages.association_fetched_successfully'),
            'data' => new AssociationResource($association),
        ]);
    }

    public function update(UpdateAssociationRequest $request, $id)
    {
        $association = Association::findOrFail($id);
        $association->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => __('messages.association_updated_successfully'),
            'data' => new AssociationResource($association),
        ]);
    }

    public function destroy($id)
    {
        $association = Association::findOrFail($id);

        if ($association->members()->exists() || $association->payments()->exists()) {
            return response()->json([
                'status' => false,
                'message' => __('messages.cannot_delete_association_with_data'),
            ], 400);
        }

        $association->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.association_deleted_successfully'),
        ]);
    }
}
