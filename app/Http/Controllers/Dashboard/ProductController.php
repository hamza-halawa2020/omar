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

    public function list()
    {
        $products = Product::with(['creator'])->get();

        return response()->json(['status' => true, 'message' => __('messages.products_fetched_successfully'), 'data' => ProductResource::collection($products)]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        $product = Product::create($data);

        return response()->json(['status' => true,  'message' => __('messages.Product_created_successfully'), 'data' => new ProductResource($product)], 201);
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

        // dd($installmentContracts);

        return view('dashboard.products.show', compact('product', 'totalCost','installmentContracts'));

    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

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
