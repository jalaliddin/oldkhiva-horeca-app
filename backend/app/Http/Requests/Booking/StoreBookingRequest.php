<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'event_time' => ['nullable', 'date_format:H:i'],
            'guest_count' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:menu_item,service'],
            'items.*.id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
