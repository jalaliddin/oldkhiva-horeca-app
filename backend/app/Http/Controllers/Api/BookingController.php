<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Booking;
use App\Models\MenuItem;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with(['items', 'invoice'])
            ->where('client_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->client_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Ruxsat yo\'q.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $booking->load(['items.itemable', 'invoice', 'client']),
        ]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $totalAmount = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            if ($item['type'] === 'menu_item') {
                $model = MenuItem::findOrFail($item['id']);
            } else {
                $model = Service::findOrFail($item['id']);
            }

            $subtotal = $model->price * $item['quantity'];
            $totalAmount += $subtotal;

            $itemsData[] = [
                'itemable_type' => get_class($model),
                'itemable_id' => $model->id,
                'item_name' => $model->name,
                'item_price' => $model->price,
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
            ];
        }

        $booking = Booking::create([
            'client_id' => $request->user()->id,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'guest_count' => $request->guest_count,
            'notes' => $request->notes,
            'total_amount' => $totalAmount,
        ]);

        $booking->items()->createMany($itemsData);

        return response()->json([
            'success' => true,
            'message' => 'Bron so\'rovi yuborildi.',
            'data' => $booking->load('items'),
        ], 201);
    }
}
