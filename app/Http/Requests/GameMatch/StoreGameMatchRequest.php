<?php

namespace App\Http\Requests\GameMatch;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreGameMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ORGANIZER) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'round' => ['required', 'string', 'max:100'],
            'team_one_id' => ['required', 'integer', 'exists:teams,id'],
            'team_two_id' => ['nullable', 'integer', 'different:team_one_id', 'exists:teams,id'],
            'scheduled_at' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'team_one_id.required' => 'Please select the first team.',
            'team_two_id.different' => 'Team two must be different from team one.',
            'scheduled_at.required' => 'Please select a match date and time.',
        ];
    }
}