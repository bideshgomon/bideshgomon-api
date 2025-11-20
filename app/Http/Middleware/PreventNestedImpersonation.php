<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventNestedImpersonation
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('impersonator_id')) {
            return redirect()->back()->with('error', 'Already in impersonation mode. Exit before starting a new one.');
        }
        return $next($request);
    }
}
