<?php

namespace App\Http\Requests\Payments;

use App\Rules\ValidStreamUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLivestreamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => [
                'required',
                Rule::in([
                    'youtube',
                    'twitch',
                    'facebook',
                ]),
            ],

            'url' => [
                'required',
                'url',
                new ValidStreamUrl(
                    $this->input('platform')
                ),
            ],

            'label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }
}