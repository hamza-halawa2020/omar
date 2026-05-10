<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function __construct(private readonly FileService $fileService)
    {
    }

    public function list(Request $request): Collection
    {
        return Product::with(['creator'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('code', 'like', '%' . $request->search . '%')
                        ->orWhere('stock', 'like', '%' . $request->search . '%');
                });
            })
            ->get();
    }

    public function create(array $data, Request $request): Product
    {
        $data['created_by'] = Auth::id();
        $data['image'] = $request->hasFile('image')
            ? $this->fileService->storePublicFile($request->file('image'), 'uploads/products')
            : null;

        return Product::create(array_filter($data, fn ($value) => $value !== null));
    }

    public function show(int $id): Product
    {
        return Product::with(['creator'])->findOrFail($id);
    }

    public function details(int $id): array
    {
        $product = Product::findOrFail($id);

        return [
            'product' => $product,
            'totalCost' => $product->stock * $product->purchase_price,
            'installmentContracts' => $product->installmentContracts,
            'transactions' => $product->transactions,
        ];
    }

    public function update(int $id, array $data, Request $request): Product
    {
        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            $this->fileService->deletePublicFile($product->image);
            $data['image'] = $this->fileService->storePublicFile($request->file('image'), 'uploads/products');
        }

        $product->update($data);

        return $product;
    }

    public function delete(int $id): Product
    {
        $product = Product::findOrFail($id);

        if ($product->installmentContracts()->exists()) {
            throw new HttpResponseException(
                response()->json(['status' => false, 'message' => __('messages.cannot_delete_Product_with_installments')], 400)
            );
        }

        $product->delete();

        return $product;
    }
}
