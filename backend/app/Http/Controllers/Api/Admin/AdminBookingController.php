<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\LandingSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with('client');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }

        $bookings = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function show(Booking $booking): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $booking->load(['client', 'items.itemable', 'invoice.payments', 'approvedBy']),
        ]);
    }

    public function approve(Request $request, Booking $booking): JsonResponse
    {
        $booking->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        $invoice = Invoice::create([
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
            'subtotal' => $booking->total_amount,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'total_amount' => $booking->total_amount,
            'paid_amount' => 0,
            'balance' => $booking->total_amount,
            'due_date' => now()->addDays(7),
            'status' => 'unpaid',
        ]);

        $this->generateInvoicePdf($invoice);

        return response()->json([
            'success' => true,
            'message' => 'Bron tasdiqlandi va invoice yaratildi.',
            'data' => [
                'booking' => $booking->fresh(),
                'invoice' => $invoice->fresh(),
            ],
        ]);
    }

    public function reject(Request $request, Booking $booking): JsonResponse
    {
        $booking->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bron rad etildi.',
            'data' => $booking->fresh(),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,cancelled,completed'],
        ]);

        $booking->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status yangilandi.',
            'data' => $booking->fresh(),
        ]);
    }

    private function generateInvoicePdf(Invoice $invoice): void
    {
        $invoice->load(['booking.items.itemable', 'client']);

        $settings = LandingSetting::whereIn('key', [
            'restaurant_name', 'restaurant_address', 'restaurant_phone',
            'restaurant_email', 'restaurant_inn', 'restaurant_bank',
            'restaurant_mfo', 'restaurant_account', 'restaurant_website',
        ])->pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'settings'));
        $path = "invoices/INV-{$invoice->invoice_number}.pdf";
        Storage::put($path, $pdf->output());
        $invoice->update(['file_path' => $path]);
    }
}
