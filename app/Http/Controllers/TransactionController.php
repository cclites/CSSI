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

class TransactionController extends Controller
{
	public function index(Request $request)
	{
		$balance = $this->api
            ->be(auth()->user())
            ->get('api/transactions/balance');

		$transactions = $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->get('api/transactions');

        return view('transactions/index')
        	->with('balance', $balance['balance'])
        	->with('transactions', $transactions);
	}

}