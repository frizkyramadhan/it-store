<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (empty($roles)) $roles = ['admin'];

        foreach ($roles as $role) {
            if (Auth::check() && Auth::user()->role === $role) {
                return $next($request);
            } elseif (Auth::check() && Auth::user()->role === null) {
                return redirect('login');
            }
        }
        return response()->view('errors.403', ['title' => '403 Error']);
    }
}
