<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::where('role', 'client');

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $clients = $query->with('deposit')->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $clients,
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $user->load(['deposit', 'bookings', 'invoices']),
        ]);
    }

    public function approve(Request $request, User $user): JsonResponse
    {
        $user->update([
            'is_active' => true,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mijoz tasdiqlandi.',
            'data' => $user->fresh(),
        ]);
    }

    public function reject(User $user): JsonResponse
    {
        $user->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Mijoz rad etildi.',
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'company_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'address' => ['sometimes', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mijoz yangilandi.',
            'data' => $user->fresh(),
        ]);
    }
}
