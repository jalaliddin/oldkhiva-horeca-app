<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use App\Models\MenuCategory;
use Illuminate\Http\JsonResponse;

class LandingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = LandingSetting::all()->keyBy('key')->map(function ($setting) {
            if ($setting->type === 'json') {
                return json_decode($setting->value, true);
            }
            if ($setting->type === 'boolean') {
                return $setting->value === 'true';
            }

            return $setting->value;
        });

        $menuCategories = MenuCategory::with(['items' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => $settings,
                'menu_categories' => $menuCategories,
            ],
        ]);
    }
}
