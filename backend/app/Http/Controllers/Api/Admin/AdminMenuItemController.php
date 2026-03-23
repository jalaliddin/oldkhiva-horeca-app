<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AdminMenuItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = MenuItem::with('category')->orderBy('sort_order')->paginate(20);

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function store(UpdateMenuItemRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu/items', 'public');
        }

        $item = MenuItem::create($data);

        return response()->json(['success' => true, 'data' => $item->load('category')], 201);
    }

    public function show(MenuItem $menuItem): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $menuItem->load('category')]);
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $request->file('image')->store('menu/items', 'public');
        }

        $menuItem->update($data);

        return response()->json(['success' => true, 'data' => $menuItem->fresh()->load('category')]);
    }

    public function destroy(MenuItem $menuItem): JsonResponse
    {
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }
        $menuItem->delete();

        return response()->json(['success' => true, 'message' => 'Taom o\'chirildi.']);
    }
}
