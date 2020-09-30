<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( auth()->user()->hasRole('admin') ) {
            return $next($request);
        }


        if ($request->ajax() || $request->wantsJson() || $request->headers->has('Authorization')) {
            return response()->json([
                'message' => 'You must be an administrator to access this endpoint',
                'status_code' => 401
            ], 401);
        }
        
        return redirect('unauthorized');
    }
}
