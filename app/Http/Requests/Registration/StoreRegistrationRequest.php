<?php

namespace App\Http\Requests\Registration;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * Only authenticated players may submit tournament registrations.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_PLAYER) ?? false;
    }

    /**
     * Validate the submitted registration data.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'team_id' => [
                'required',
                'integer',
                'exists:teams,id',
            ],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'team_id.required' => 'Please select a team to register.',
            'team_id.integer' => 'The selected team is invalid.',
            'team_id.exists' => 'The selected team does not exist.',
        ];
    }
}