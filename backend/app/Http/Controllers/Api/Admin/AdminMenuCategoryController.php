<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateMenuCategoryRequest;
use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMenuCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = MenuCategory::withCount('items')->orderBy('sort_order')->get();

        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function store(UpdateMenuCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu/categories', 'public');
        }

        $category = MenuCategory::create($data);

        return response()->json(['success' => true, 'data' => $category], 201);
    }

    public function show(MenuCategory $menuCategory): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $menuCategory->load('items')]);
    }

    public function update(UpdateMenuCategoryRequest $request, MenuCategory $menuCategory): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($menuCategory->image) {
                Storage::disk('public')->delete($menuCategory->image);
            }
            $data['image'] = $request->file('image')->store('menu/categories', 'public');
        }

        $menuCategory->update($data);

        return response()->json(['success' => true, 'data' => $menuCategory->fresh()]);
    }

    public function destroy(MenuCategory $menuCategory): JsonResponse
    {
        if ($menuCategory->image) {
            Storage::disk('public')->delete($menuCategory->image);
        }
        $menuCategory->delete();

        return response()->json(['success' => true, 'message' => 'Kategoriya o\'chirildi.']);
    }
}
