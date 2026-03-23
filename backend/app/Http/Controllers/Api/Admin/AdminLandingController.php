<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLandingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = LandingSetting::all();

        return response()->json(['success' => true, 'data' => $settings]);
    }

    public function update(Request $request): JsonResponse
    {
        $settings = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable'],
        ]);

        foreach ($settings['settings'] as $setting) {
            LandingSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Sozlamalar saqlandi.',
        ]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'key' => ['required', 'string'],
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('image')->store('landing', 'public');

        LandingSetting::updateOrCreate(
            ['key' => $request->key],
            ['value' => $path, 'type' => 'image']
        );

        return response()->json([
            'success' => true,
            'message' => 'Rasm yuklandi.',
            'data' => ['path' => $path, 'url' => asset("storage/{$path}")],
        ]);
    }
}
