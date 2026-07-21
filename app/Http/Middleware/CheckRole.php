<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
            }

            return redirect()->route('login');
        }

        if (! empty($roles) && ! in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized. You do not have the required role to access this page.');
        }

        return $next($request);
    }
}
