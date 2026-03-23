<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function clients(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'all'); // all | debtors | depositors
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $clients = User::query()
            ->where('role', 'client')
            ->with(['deposit', 'invoices', 'bookings'])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%");
            }))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->get()
            ->map(function (User $client) {
                $deposit = (float) ($client->deposit?->balance ?? 0);
                $debt = (float) $client->invoices->sum('balance');
                $totalInvoiced = (float) $client->invoices->sum('total_amount');
                $totalPaid = (float) $client->invoices->sum('paid_amount');

                return [
                    'id' => $client->id,
                    'company_name' => $client->company_name,
                    'contact_person' => $client->contact_person,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'is_active' => $client->is_active,
                    'contract_agreed' => $client->contract_agreed,
                    'registered_at' => $client->created_at->format('Y-m-d'),
                    'deposit' => $deposit,
                    'debt' => $debt,
                    'total_invoiced' => $totalInvoiced,
                    'total_paid' => $totalPaid,
                    'invoice_count' => $client->invoices->count(),
                    'booking_count' => $client->bookings->count(),
                    'financial_status' => $debt > 0 ? 'debtor' : ($deposit > 0 ? 'depositor' : 'clear'),
                ];
            });

        if ($filter === 'debtors') {
            $clients = $clients->filter(fn ($c) => $c['debt'] > 0);
        } elseif ($filter === 'depositors') {
            $clients = $clients->filter(fn ($c) => $c['deposit'] > 0);
        }

        $summary = [
            'total_clients' => $clients->count(),
            'total_debtors' => $clients->where('financial_status', 'debtor')->count(),
            'total_depositors' => $clients->where('financial_status', 'depositor')->count(),
            'total_debt' => round($clients->sum('debt'), 2),
            'total_deposit' => round($clients->sum('deposit'), 2),
            'total_invoiced' => round($clients->sum('total_invoiced'), 2),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'clients' => $clients->values(),
                'summary' => $summary,
            ],
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $invoices = Invoice::with('client', 'booking')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'total' => $invoices->count(),
            'unpaid' => $invoices->where('status', 'unpaid')->count(),
            'partial' => $invoices->where('status', 'partial')->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'overdue' => $invoices->where('status', 'overdue')->count(),
            'total_amount' => round($invoices->sum('total_amount'), 2),
            'total_paid' => round($invoices->sum('paid_amount'), 2),
            'total_balance' => round($invoices->sum('balance'), 2),
        ];

        return response()->json([
            'success' => true,
            'data' => compact('invoices', 'summary'),
        ]);
    }
}
