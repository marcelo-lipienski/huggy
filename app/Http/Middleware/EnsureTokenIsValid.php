<?php

namespace App\Http\Middleware;

use App\Models\Reader;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->input('token');

        if (! $token) {
            return new Response('', 404);
        }

        try {
            $reader = Reader::find($request->route('id'));
        } catch (Exception $e) {
            return new Response('', 404);
        }

        if ($reader?->token === $token) {
            return $next($request);
        }

        return new Response('', 404);
    }
}
