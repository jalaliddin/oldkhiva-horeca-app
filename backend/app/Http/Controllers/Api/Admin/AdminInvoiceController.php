<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminInvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['client', 'booking']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $invoices = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json(['success' => true, 'data' => $invoices]);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $invoice->load(['client', 'booking.items.itemable', 'payments.createdBy']),
        ]);
    }

    public function download(Invoice $invoice): StreamedResponse|JsonResponse
    {
        if (! $invoice->file_path || ! Storage::exists($invoice->file_path)) {
            return response()->json(['success' => false, 'message' => 'Fayl topilmadi.'], 404);
        }

        return Storage::download($invoice->file_path, "Invoice-{$invoice->invoice_number}.pdf");
    }
}
