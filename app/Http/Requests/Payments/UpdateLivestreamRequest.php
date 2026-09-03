<?php

namespace App\Http\Requests\Payments;

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
            ],


            'label' => [
                'nullable',
                'string',
                'max:255',
            ],


            'is_active' => [
                'sometimes',
                'boolean',
            ],

        ];
    }

}