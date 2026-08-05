<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Anyone may submit the login form.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for ArenaSync login.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],

            'remember' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}