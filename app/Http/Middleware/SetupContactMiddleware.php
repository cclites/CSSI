<?php

namespace App\Http\Middleware;

use Closure;

class SetupContactMiddleware
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
        if ( auth()->user()->is_setup_contact ) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson() || $request->headers->has('Authorization')) {
            return response()->json([
                'message' => 'You must enter contact information before proceeding',
                'status_code' => 401
            ], 401);
        }
        
        flash('You must enter contact information before proceeding');

        return redirect('settings/contact');
    }
}
