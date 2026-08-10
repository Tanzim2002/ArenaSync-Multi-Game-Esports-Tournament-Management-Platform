<?php
// app/Http/Requests/Payments/UpdateLivestreamRequest.php

namespace App\Http\Requests\Payments;

class UpdateLivestreamRequest extends StoreLivestreamRequest
{
    public function authorize(): bool
    {
        return true; // enforced via policy in controller
    }

    public function rules(): array
    {
        return [
            'platform' => ['required', 'in:youtube,twitch,facebook'],
            'url'      => ['required', 'string', 'max:255', new ValidStreamUrl($this->input('platform'))],
            'label'    => ['nullable', 'string', 'max:100'],
        ];
    }
}