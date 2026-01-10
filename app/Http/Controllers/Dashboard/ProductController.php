<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class ProductController extends BaseController
{
    public function __construct()
    {
        $this->middleware('check.permission:products_index')->only('index', 'list');
        $this->middleware('check.permission:products_store')->only('store');
        $this->middleware('check.permission:products_show')->only('show');
        $this->middleware('check.permission:products_update')->only('update');
        $this->middleware('check.permission:products_destroy')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.products.index');
    }

    public function list(\Illuminate\Http\Request $request)
    {
        $products = Product::with(['creator'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('code', 'like', '%' . $request->search . '%')
                        ->orWhere('stock', 'like', '%' . $request->search . '%');
                });
            })
            ->get();

        return response()->json(['status' => true, 'message' => __('messages.products_fetched_successfully'), 'data' => ProductResource::collection($products)]);
    }

    // public function store(StoreProductRequest $request)
    // {
    //     $data = $request->validated();
    //     $data['created_by'] = Auth::id();

    //     $product = Product::create($data);

    //     return response()->json(['status' => true,  'message' => __('messages.Product_created_successfully'), 'data' => new ProductResource($product)], 201);
    // }

    public function store(StoreProductRequest $request)
    {

        $data = $request->validated();
        // dd( $request->all());
        $data['created_by'] = Auth::id();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'uploads/products/'.$imageName;
        }

        $product = Product::create($data);

        return response()->json([
            'status' => true,
            'message' => __('messages.Product_created_successfully'),
            'data' => new ProductResource($product),
        ], 201);
    }

    public function show($id)
    {
        $product = Product::with(['creator'])->findOrFail($id);

        return response()->json(['status' => true, 'message' => __('messages.Product_fetched_successfully'), 'data' => new ProductResource($product)]);
    }

    public function details($id)
    {
        $product = Product::findOrFail($id);
        $totalCost = $product->stock * $product->purchase_price;
        $installmentContracts = $product->installmentContracts;
        $transactions = $product->transactions;

        // dd($installmentContracts);

        return view('dashboard.products.show', compact('product', 'totalCost', 'installmentContracts', 'transactions'));

    }

    public function update(UpdateProductRequest $request, $id)
    {
        
        $product = Product::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            $image = $request->file('image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'uploads/products/'.$imageName;
        }

        $product->update($data);

        return response()->json(['status' => true, 'message' => __('messages.Product_updated_successfully'), 'data' => new ProductResource($product)]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->installmentContracts()->exists()) {
            return response()->json(['status' => false, 'message' => __('messages.cannot_delete_Product_with_installments')], 400);
        }

        $product->delete();

        return response()->json(['status' => true, 'message' => __('messages.Product_deleted_successfully')]);
    }
}
