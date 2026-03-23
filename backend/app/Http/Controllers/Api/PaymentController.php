<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function clientIndex(Request $request): JsonResponse
    {
        $payments = Payment::where('client_id', $request->user()->id)
            ->with('invoice')
            ->orderByDesc('payment_date')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }
}
