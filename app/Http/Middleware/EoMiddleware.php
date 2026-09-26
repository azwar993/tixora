<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EoMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if ($request->user()?->role !== 'eo') {
            abort(403);
        }

        return $next($request);
    }
}
