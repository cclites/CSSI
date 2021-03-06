<?php

namespace App\Http\Controllers;

// Models
use App\Models\User;

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

class TypeController extends Controller
{
	public function index(Request $request){
		
		return view('admin/types/index')
            ->with('request', $request);
			
	}
	
	public function toggle(Request $request){
		
		try {
            return $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->get('api/admin/types/toggle');
        } catch (UpdateResourceFailedException $e) {
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }
		
	}

}