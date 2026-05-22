<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Trebuie să te autentifici!');
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'Nu ai acces la această secțiune!');
        }

        return $next($request);
    }
}