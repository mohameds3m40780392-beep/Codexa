<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized access. Admins only.'
            ], 403);
        }
        return $next($request);
    }
}
