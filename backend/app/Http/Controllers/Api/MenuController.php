<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = MenuCategory::with(['items' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function publicMenu(): JsonResponse
    {
        $categories = MenuCategory::with(['items' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
