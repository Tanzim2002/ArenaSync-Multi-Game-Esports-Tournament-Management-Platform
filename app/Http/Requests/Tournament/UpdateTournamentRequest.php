<?php

namespace App\Http\Requests\Tournament;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTournamentRequest extends FormRequest
{
    /**
     * Only the organizer who owns a draft tournament may update it.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $tournament = $this->route('tournament');

        if (
            $user === null ||
            ! $user->hasRole(User::ROLE_ORGANIZER) ||
            ! $tournament instanceof Tournament
        ) {
            return false;
        }

        return $tournament->organizer_id === $user->id
            && $tournament->status === Tournament::STATUS_DRAFT;
    }

    /**
     * Validate a tournament update.
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