<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
            'company_name' => ['required', 'string', 'max:255'],
            'inn' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:20'],
            'director_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string'],
            'mfo' => ['required', 'string', 'max:10'],
            'bank_account' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Bu email allaqachon ro\'yhatdan o\'tgan.',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo\'lishi kerak.',
            'password.confirmed' => 'Parollar mos emas.',
        ];
    }
}
