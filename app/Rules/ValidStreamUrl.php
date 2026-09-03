<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidStreamUrl implements ValidationRule
{
    protected array $allowedHosts = [
        'youtube'  => ['youtube.com', 'www.youtube.com', 'youtu.be'],
        'twitch'   => ['twitch.tv', 'www.twitch.tv'],
        'facebook' => ['facebook.com', 'www.facebook.com', 'fb.watch'],
    ];

    public function __construct(protected ?string $platform) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! filter_var($value, FILTER_VALIDATE_URL) || ! str_starts_with($value, 'https://')) {
            $fail('The stream link must be a valid, secure (https) URL.');
            return;
        }

        $host     = parse_url($value, PHP_URL_HOST);
        $expected = $this->allowedHosts[$this->platform] ?? [];

        if ($host === null || ! in_array($host, $expected, true)) {
            $fail("The stream link does not match a valid {$this->platform} URL.");
        }
    }
}