<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $deposit = Deposit::firstOrCreate(
            ['client_id' => $request->user()->id],
            ['balance' => 0]
        );

        return response()->json([
            'success' => true,
            'data' => $deposit,
        ]);
    }
}
