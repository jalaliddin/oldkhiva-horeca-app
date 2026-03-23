<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function clientIndex(Request $request): JsonResponse
    {
        $invoices = Invoice::with('booking')
            ->where('client_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $invoices,
        ]);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->client_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Ruxsat yo\'q.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $invoice->load(['booking.items.itemable', 'client', 'payments']),
        ]);
    }

    public function download(Request $request, Invoice $invoice): StreamedResponse|JsonResponse
    {
        if ($invoice->client_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Ruxsat yo\'q.'], 403);
        }

        if (! $invoice->file_path || ! Storage::exists($invoice->file_path)) {
            return response()->json(['success' => false, 'message' => 'Fayl topilmadi.'], 404);
        }

        return Storage::download($invoice->file_path, "Invoice-{$invoice->invoice_number}.pdf");
    }
}
