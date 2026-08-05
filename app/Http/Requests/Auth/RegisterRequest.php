<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Anyone may submit the public registration form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for ArenaSync registration.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'role' => [
                'required',
                Rule::in([
                    User::ROLE_PLAYER,
                    User::ROLE_ORGANIZER,
                ]),
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }
}