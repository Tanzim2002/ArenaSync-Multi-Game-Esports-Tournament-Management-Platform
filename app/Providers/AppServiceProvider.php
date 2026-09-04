<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            Vite::createAssetPathsUsing(function (string $path, ?bool $secure): string {
                $baseUrl = rtrim((string) config('app.url'), '/');

                return "{$baseUrl}/{$path}";
            });
        }
    }
}
