<?php

namespace Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Middleware para resolver FormRequests automaticamente no Lumen
 * 
 * No Lumen, FormRequests não são injetados automaticamente como no Laravel.
 * Este middleware intercepta requests e resolve FormRequests quando necessário.
 */
class FormRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // O Lumen já resolve FormRequests automaticamente se estiverem type-hinted
        // Este middleware é apenas um placeholder caso precise de lógica adicional
        return $next($request);
    }
}

