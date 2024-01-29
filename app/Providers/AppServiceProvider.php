<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Filament\Support\Facades\FilamentColor;

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
/*          FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Amber,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]); */

        Response::macro('success', function ($data = null, $developerMessage = null, $userMessage = null, $httpCode = 200) {
            return response()->json([
                'status' => 'success',
                'data' => $data,
                'developer_message' => $developerMessage,
                'user_message' => $userMessage,
            ], $httpCode);
        });

        Response::macro('fail', function ($data = null, $developerMessage = null, $userMessage = null, $httpCode = 400) {
            return response()->json([
                'status' => 'fail',
                'data' => $data,
                'developer_message' => $developerMessage,
                'user_message' => $userMessage,
            ], $httpCode);
        });

        Response::macro('error', function ($data = null, $developerMessage = null, $userMessage = null, $httpCode = 500) {
            return response()->json([
                'status' => 'error',
                'data' => $data,
                'developer_message' => $developerMessage,
                'user_message' => $userMessage,
            ], $httpCode);
        });
    }
}
