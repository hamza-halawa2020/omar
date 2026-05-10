<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ProductController extends BaseController
{
    public function __construct(private readonly ProductService $productService)
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

    public function list(Request $request)
    {
        $products = $this->productService->list($request);

        return response()->json(['status' => true, 'message' => __('messages.products_fetched_successfully'), 'data' => ProductResource::collection($products)]);
    }


    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated(), $request);

        return response()->json(['status' => true,'message' => __('messages.Product_created_successfully'),'data' => new ProductResource($product)], 201);
    }

    public function show($id)
    {
        $product = $this->productService->show((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.Product_fetched_successfully'), 'data' => new ProductResource($product)]);
    }

    public function details($id)
    {
        $details = $this->productService->details((int) $id);

        return view('dashboard.products.show', $details);
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update((int) $id, $request->validated(), $request);

        return response()->json(['status' => true, 'message' => __('messages.Product_updated_successfully'), 'data' => new ProductResource($product)]);
    }

    public function destroy($id)
    {
        $this->productService->delete((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.Product_deleted_successfully')]);
    }
}
