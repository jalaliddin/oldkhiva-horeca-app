<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:users,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'type' => ['required', 'in:invoice_payment,deposit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer,card'],
            'notes' => ['nullable', 'string'],
            'reference' => ['nullable', 'string', 'max:100'],
        ];
    }
}
