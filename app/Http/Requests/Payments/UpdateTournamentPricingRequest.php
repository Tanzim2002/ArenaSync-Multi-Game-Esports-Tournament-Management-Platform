<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTournamentPricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ownership is checked in the controller via TournamentPricingPolicy
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_paid' => $this->boolean('is_paid')]);
    }

    public function rules(): array
    {
        return [
            'is_paid'   => ['required', 'boolean'],
            'entry_fee' => ['nullable', 'numeric', 'required_if:is_paid,true', 'min:1', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'entry_fee.required_if' => 'Entry fee is required when the tournament is marked as paid.',
            'entry_fee.min'         => 'Entry fee must be at least 1 when the tournament is paid.',
        ];
    }
}