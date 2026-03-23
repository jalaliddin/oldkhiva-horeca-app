<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['client', 'invoice', 'createdBy']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $payments = $query->orderByDesc('payment_date')
            ->paginate($request->get('per_page', 15));

        return response()->json(['success' => true, 'data' => $payments]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = Payment::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'To\'lov kiritildi.',
            'data' => $payment->load(['client', 'invoice']),
        ], 201);
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();

        return response()->json(['success' => true, 'message' => 'To\'lov o\'chirildi.']);
    }
}
