<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\CreateBackup;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class CategoryController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('check.permission:categories_index')->only('index');
        // $this->middleware('check.permission:categories_update')->only(['edit', 'update']);
    }



    public function index()
    {
        return view('dashboard.categories.index');
    }

    public function list()
    {
        $categories = Category::with(['parent', 'children', 'creator'])->get();

        return response()->json(['status' => true, 'message' => __('messages.categories_fetched_successfully'), 'data' => CategoryResource::collection($categories)]);
    }



    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();

        $category = Category::create($data);
        
        //event(new CreateBackup());

        return response()->json(['status' => true,  'message' => __('messages.category_created_successfully'), 'data' => new CategoryResource($category),], 201);
    }

    public function show($id)
    {
        $category = Category::with(['parent', 'children', 'creator'])->findOrFail($id);

        return response()->json(['status' => true, 'message' => __('messages.category_fetched_successfully'), 'data' => new CategoryResource($category),]);
    }


    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();

        $category->update($data);

        //event(new CreateBackup);


        return response()->json(['status' => true, 'message' => __('messages.category_updated_successfully'), 'data' => new CategoryResource($category),]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->categoryPaymentWay()->exists() || $category->subCategoryPaymentWay()->exists()) {
            return response()->json(['status' => false, 'message' => __('messages.cannot_delete_category_with_payment_way')], 400);
        }

        $category->delete();

        //event(new CreateBackup);


        return response()->json(['status' => true, 'message' => __('messages.category_deleted_successfully')]);
    }
}
