<?php

namespace App\Http\Controllers;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
	public function index(Request $request)
	{
		return view('utilities/index');
	}


}