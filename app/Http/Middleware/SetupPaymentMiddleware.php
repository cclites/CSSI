<?php

namespace App\Http\Middleware;

use Closure;

class SetupPaymentMiddleware
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
    	return $next($request);
		
    	return redirect('settings/billing');
		
        if ( auth()->user()->stripe_customer_id ) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson() || $request->headers->has('Authorization')) {
            return response()->json([
                'message' => 'You must setup a payment method before proceeding',
                'status_code' => 401
            ], 401);
        }
        
        flash('You must setup a payment method before proceeding');
        
        return redirect('settings/billing');
    }
}
