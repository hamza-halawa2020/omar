<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Routing\Controller as BaseController;

class CategoryController extends BaseController
{
    public function __construct(private readonly CategoryService $categoryService)
    {
        $this->middleware('check.permission:categories_index')->only('index', 'list');
        $this->middleware('check.permission:categories_store')->only('store');
        $this->middleware('check.permission:categories_update')->only('update');
        $this->middleware('check.permission:categories_destroy')->only('destroy');
    }

    public function index()
    {
        return view('dashboard.categories.index');
    }

    public function list()
    {
        $categories = $this->categoryService->list();

        return response()->json(['status' => true, 'message' => __('messages.categories_fetched_successfully'), 'data' => CategoryResource::collection($categories)]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->store($request->validated());

        return response()->json(['status' => true,  'message' => __('messages.category_created_successfully'), 'data' => new CategoryResource($category)], 201);
    }

    public function show($id)
    {
        $category = $this->categoryService->show((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.category_fetched_successfully'), 'data' => new CategoryResource($category)]);
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->update((int) $id, $request->validated());

        return response()->json(['status' => true, 'message' => __('messages.category_updated_successfully'), 'data' => new CategoryResource($category)]);
    }

    public function destroy($id)
    {
        $this->categoryService->destroy((int) $id);

        return response()->json(['status' => true, 'message' => __('messages.category_deleted_successfully')]);
    }
}
