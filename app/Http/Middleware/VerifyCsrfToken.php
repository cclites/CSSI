<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
	 * 
	 * Ignorning these requests from the CSSI Mobile app since they won't have a Csrf token
	 * 
     */
    protected $except = [
        'cssi/signup',
        'cssi/login',
        'cssi/contact/',
        'cssi/ney/*'
    ];
}
