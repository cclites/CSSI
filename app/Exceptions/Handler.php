<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Log;
use Session;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
     
    //not used
    public function report(Exception $exception)
    {
        if ($this->shouldReport($exception)) {
            //app('sneaker')->captureException($exception);
        }
        
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {

        //This is a hack-fix. When a user is logged out and they try to view a 
        //page, the app would throw an error. This redirects the user to the
        //login view. Have not noticed any negative side effects, but I'm sure
        //there is a better way to do it.
        if(!app('auth')->check()){
			return parent::render($request, $e);
        }

        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 400;
        $data = [];
        return response()->view('errors.generic', $data, $statusCode);

    }
}
