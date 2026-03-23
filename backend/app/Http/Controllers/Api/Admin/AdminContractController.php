<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminContractController extends Controller
{
    public function index(): JsonResponse
    {
        $contracts = ContractTemplate::orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $contracts,
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        ContractTemplate::where('is_active', true)->update(['is_active' => false]);

        $path = $request->file('file')->store('contracts', 'public');

        $contract = ContractTemplate::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shartnoma yuklandi.',
            'data' => $contract,
        ], 201);
    }

    public function activate(ContractTemplate $contract): JsonResponse
    {
        ContractTemplate::where('is_active', true)->update(['is_active' => false]);
        $contract->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Shartnoma faollashtirildi.',
            'data' => $contract->fresh(),
        ]);
    }
}
