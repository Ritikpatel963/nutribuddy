<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutPlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer', 'exists:customer_addresses,id'],
            'coupon_code' => ['nullable', 'string', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
            'payment_method' => ['required', 'in:cod'],
            'checkout_token' => ['nullable', 'string', 'max:120'],
        ];
    }
}
