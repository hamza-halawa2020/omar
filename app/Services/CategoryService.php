<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function list(): Collection
    {
        return Category::with(['parent', 'children', 'creator'])->get();
    }

    public function store(array $data): Category
    {
        $data['created_by'] = Auth::id();

        return Category::create($data);
    }

    public function show(int $id): Category
    {
        return Category::with(['parent', 'children', 'creator'])->findOrFail($id);
    }

    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);

        return $category;
    }

    public function destroy(int $id): void
    {
        $category = Category::findOrFail($id);

        if ($category->categoryPaymentWay()->exists() || $category->subCategoryPaymentWay()->exists()) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => __('messages.cannot_delete_category_with_payment_way')], 400));
        }

        $category->delete();
    }
}
