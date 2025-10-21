<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TnvCategoryHelper;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = TnvCategoryHelper::getAll($request->all());
        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category = TnvCategoryHelper::save($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm danh mục thành công',
            'data' => $category,
        ]);
    }

    public function show(Request $request, $key)
    {
        $includeChildren = $request->input('include_children', true);
        $category = is_numeric($key)
        ? TnvCategoryHelper::getById($key)
        : TnvCategoryHelper::getBySlug($key, $includeChildren);
        
        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category = TnvCategoryHelper::save($validated, $id);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật danh mục thành công',
            'data' => $category,
        ]);
    }

    public function destroy($id)
    {
        TnvCategoryHelper::delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Xóa danh mục thành công',
        ]);
    }
}