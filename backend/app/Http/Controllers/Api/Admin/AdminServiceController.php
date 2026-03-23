<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class AdminServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::all();

        return response()->json(['success' => true, 'data' => $services]);
    }

    public function store(UpdateServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());

        return response()->json(['success' => true, 'data' => $service], 201);
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $service]);
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        return response()->json(['success' => true, 'data' => $service->fresh()]);
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(['success' => true, 'message' => 'Xizmat o\'chirildi.']);
    }
}
