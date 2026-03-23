<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalClients = User::where('role', 'client')->count();
        $pendingClients = User::where('role', 'client')->where('is_active', false)->count();
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $totalRevenue = Payment::where('type', 'invoice_payment')->sum('amount');
        $unpaidInvoices = Invoice::whereIn('status', ['unpaid', 'partial', 'overdue'])->count();

        $monthlyRevenue = collect(range(5, 0))->map(function ($monthsAgo) {
            $month = Carbon::now()->subMonths($monthsAgo);

            return [
                'month' => $month->format('Y-m'),
                'label' => $month->format('M Y'),
                'amount' => Payment::where('type', 'invoice_payment')
                    ->whereYear('payment_date', $month->year)
                    ->whereMonth('payment_date', $month->month)
                    ->sum('amount'),
            ];
        })->values();

        $bookingStatusBreakdown = Booking::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentBookings = Booking::with('client')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $pendingClientsList = User::where('role', 'client')
            ->where('is_active', false)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_clients' => $totalClients,
                    'pending_clients' => $pendingClients,
                    'total_bookings' => $totalBookings,
                    'pending_bookings' => $pendingBookings,
                    'total_revenue' => $totalRevenue,
                    'unpaid_invoices' => $unpaidInvoices,
                    'monthly_revenue' => $monthlyRevenue,
                    'booking_status_breakdown' => $bookingStatusBreakdown,
                ],
                'recent_bookings' => $recentBookings,
                'pending_clients' => $pendingClientsList,
            ],
        ]);
    }
}
