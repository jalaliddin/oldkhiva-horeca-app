<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'is_active' => false,
            'company_name' => $request->company_name,
            'inn' => $request->inn,
            'mfo' => $request->mfo,
            'bank_account' => $request->bank_account,
            'bank_name' => $request->bank_name,
            'director_name' => $request->director_name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        Deposit::create(['client_id' => $user->id, 'balance' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Arizangiz qabul qilindi. Admin tasdiqlashini kuting.',
            'data' => ['user' => $user],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email yoki parol noto\'g\'ri.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Muvaffaqiyatli kirdingiz.',
            'data' => [
                'token' => $token,
                'user' => $user->load('deposit'),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Muvaffaqiyatli chiqdingiz.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('deposit'),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'address' => ['sometimes', 'string'],
            'company_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $request->user()->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil yangilandi.',
            'data' => $request->user()->fresh(),
        ]);
    }
}
