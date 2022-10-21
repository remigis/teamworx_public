<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Privilege
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, $requiredPrivilege)
    {
        if (Auth::user()->havePrivilegeTo($requiredPrivilege)) {
            return $next($request);
        } else {
            if ($request->wantsJson()) {
                return response()->json(['message' => "You don't have required permissions"], 403);
            } else {
                return response()->view('errors.403', [], 403);
            }

        }

    }
}
