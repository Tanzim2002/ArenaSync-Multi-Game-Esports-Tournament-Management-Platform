<?php

namespace App\Http\Requests\Game;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGameRequest extends FormRequest
{
    /**
     * Only administrators may update games.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ADMIN) ?? false;
    }

    /**
     * Validate an existing game update.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('games', 'name')
                    ->ignore($this->route('game')),
            ],

            'genre' => [
                'required',
                'string',
                'max:100',
            ],

            'platform' => [
                'required',
                'string',
                'max:100',
            ],

            'rules' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'team_size' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}