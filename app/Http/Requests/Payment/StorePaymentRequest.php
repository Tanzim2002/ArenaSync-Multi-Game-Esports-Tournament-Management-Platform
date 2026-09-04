<?php

namespace App\Http\Requests\Payment;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(
            User::ROLE_PLAYER
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            'method' => [
                'required',
                'string',
                'max:100',
            ],

            'reference' => [
                'required',
                'string',
                'max:150',
                'unique:payments,reference',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'
                => 'Please enter the amount you paid.',

            'amount.min'
                => 'The amount must be greater than zero.',

            'method.required'
                => 'Please select a payment method.',

            'reference.required'
                => 'Please enter the transaction reference.',

            'reference.unique'
                => 'This transaction reference has already been submitted.',
        ];
    }
}