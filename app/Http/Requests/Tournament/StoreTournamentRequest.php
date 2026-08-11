<?php

namespace App\Http\Requests\Tournament;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreTournamentRequest extends FormRequest
{
    /**
     * Only authenticated organizers may create tournaments.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ORGANIZER) ?? false;
    }

    /**
     * Validate a new tournament.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'game_id' => [
                'required',
                'integer',
                'exists:games,id',
            ],

            'title' => [
                'required',
                'string',
                'max:180',
            ],

            'description' => [
                'required',
                'string',
                'min:20',
                'max:10000',
            ],

            'registration_deadline' => [
                'required',
                'date',
                'after:now',
            ],

            'start_at' => [
                'required',
                'date',
                'after:registration_deadline',
            ],

            'end_at' => [
                'required',
                'date',
                'after:start_at',
            ],

            'rules' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],

            'prize_pool' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'team_limit' => [
                'required',
                'integer',
                'min:2',
                'max:1000',
            ],

            'match_format' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }
}