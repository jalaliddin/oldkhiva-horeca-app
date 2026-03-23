<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractController extends Controller
{
    public function getActive(): JsonResponse
    {
        $contract = ContractTemplate::where('is_active', true)->latest()->first();

        return response()->json([
            'success' => true,
            'data' => $contract,
        ]);
    }

    public function agree(Request $request): JsonResponse
    {
        $request->user()->update(['contract_agreed' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Shartnoma tasdiqlandi.',
        ]);
    }

    public function download(Request $request): StreamedResponse|JsonResponse
    {
        $contract = ContractTemplate::where('is_active', true)->latest()->first();

        if (! $contract || ! Storage::disk('public')->exists($contract->file_path)) {
            return response()->json(['success' => false, 'message' => 'Shartnoma fayli topilmadi.'], 404);
        }

        return Storage::disk('public')->download($contract->file_path, 'Shartnoma.pdf');
    }
}
