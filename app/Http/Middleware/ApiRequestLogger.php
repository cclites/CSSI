<?php

namespace App\Http\Middleware;

// Models
use App\Models\Log;

use Route;
use JWTAuth;
use Closure;


class ApiRequestLogger
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
        $excludes = [
            'api/texts/unseen',
            'api/logs',
        ];
		
        $response = $next($request);

		/*
        if (! in_array($request->path(), $excludes)) {
            $log = new Log;
            $log->method = $request->method();
            $log->path = $request->path();
            $log->full_url = $request->fullUrl();
            $log->ip = $request->ip();
            $log->request = $request->getContent();
            if ($request->hasHeader('Authorization')) {
                $user = JWTAuth::parseToken()->authenticate();
                $log->user_id = $user ? $user->id : null;
            } elseif (auth()->check()) {
                $log->user_id = auth()->id();
            }
            $log->code = $response->status();
            $log->exception = $response->exception;
            $log->response = json_encode($response);
            $log->save();
        }
        */ 
        return $response;
    }
}
