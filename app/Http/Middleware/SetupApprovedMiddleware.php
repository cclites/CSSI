<?php

namespace App\Http\Middleware;

use Closure;

class SetupApprovedMiddleware
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
        if ( auth()->user()->is_approved ) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson() || $request->headers->has('Authorization')) {
            return response()->json([
                'message' => 'Your account must be approved by an administrator before proceeding',
                'status_code' => 401
            ], 401);
        }
        
        return redirect('settings/awaiting_approval');
    }
}
