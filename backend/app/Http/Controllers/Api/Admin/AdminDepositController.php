<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDepositController extends Controller
{
    public function index(): JsonResponse
    {
        $deposits = Deposit::with('client')->orderByDesc('balance')->get();

        return response()->json(['success' => true, 'data' => $deposits]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer,card'],
            'notes' => ['nullable', 'string'],
            'reference' => ['nullable', 'string'],
        ]);

        $payment = Payment::create([
            'client_id' => $request->client_id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'reference' => $request->reference,
            'created_by' => $request->user()->id,
        ]);

        $deposit = Deposit::where('client_id', $request->client_id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Depozit kiritildi.',
            'data' => [
                'payment' => $payment,
                'deposit' => $deposit,
            ],
        ], 201);
    }
}
