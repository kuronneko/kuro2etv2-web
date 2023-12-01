<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ( !$request->hasHeader('x-api-key') || $request->header('x-api-key') != config('app.api_key') ) {
            return response()->fail(
                developerMessage: 'No autorizado. Api key inválida.',
                userMessage: ''
            );
        }

        return $next($request);
    }
}
