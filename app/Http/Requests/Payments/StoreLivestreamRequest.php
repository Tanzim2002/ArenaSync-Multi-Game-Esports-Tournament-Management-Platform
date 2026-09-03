<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivestreamRequest extends FormRequest
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
                'in:youtube,twitch',
            ],


            'url' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {

                    $platform = $this->input('platform');


                    if (
                        $platform === 'youtube'
                        && !str_contains($value, 'youtube.com')
                    ) {
                        $fail('Invalid YouTube URL.');
                    }


                    if (
                        $platform === 'twitch'
                        && !str_contains($value, 'twitch.tv')
                    ) {
                        $fail('Invalid Twitch URL.');
                    }

                },
            ],


            'label' => [
                'nullable',
                'string',
                'max:255',
            ],

        ];
    }
}