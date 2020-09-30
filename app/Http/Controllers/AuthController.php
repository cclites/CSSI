<?php

namespace App\Http\Controllers;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Dingo\Api\Exception\InternalHttpException;

use Auth;
use JWTAuth;
use Carbon\Carbon;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;
use Log;
use Response;

class AuthController extends Controller
{

    public function signupForm(Request $request)
    {
        if (Auth::check()) {
            return redirect(secure_url('/'));
        }

        return view('auth/signup')
            ->with('request', $request);
    }

    public function signup(Request $request)
    {
        try {
            $user = $this->api
                ->with($request->all())
                ->post('api/auth/signup');

            Auth::login($user, true);
        } catch (StoreResourceFailedException $e) {
            return back()
                ->withErrors($e->getErrors())
                ->withInput($request->all());
        }

        return redirect(secure_url('settings/contact'));
    }
	
	public function appSignup(Request $request){
		
		Log::info("CSSI appSignup");
		
		try {
            $user = $this->api
                ->with($request->all())
                ->post('api/auth/signup');

            Auth::login($user, true);
			
			//set roles for user
			Auth::user()->assignRole('apiauth');
			Auth::user()->assignRole('cssi_app');
			
        } catch (StoreResourceFailedException $e) {
        	
			Log::info("CSSI App Signup Failure");
			//Log::info(json_encode($e->getErrors()));
        	
			return Response::json(array(
						            'status' => 0,
						            'message' => "CSSI App Signup Failure.",
									'errors' => $e->getErrors()),
						            200
						          );								
        }

        Log::info("CSSI App Signup Success");
		
		return Response::json(array(
						            'status' => 1,
						            'message' => "Signup Success.",
									'key' => Auth::user()->key),
						            200
						          );	
	}


    public function loginForm(Request $request)
    {
        if (Auth::check()) {
            return redirect(secure_url('/'));
        }

        return view('auth/login')
            ->with('request', $request);
    }

    public function login(Request $request)
    {
        try {
            $token = $this->api
                ->with($request->all())
                ->post('api/auth');

            $user = JWTAuth::toUser($token['token']);
            Auth::login($user, true);
			
        } catch (ResourceException $e) {
            return back()
                ->withErrors($e->getErrors())
                ->withInput($request->all());
        } catch (InternalHttpException $e) {
            flash('Wrong email or password');
            return back()
                ->withInput($request->all());
        }

        return redirect()->intended(secure_url('/'));
    }
	
	public function appLogin(Request $request){
		
		Log::info("appLogin");
		
		try {
			
            $token = $this->api
                ->with($request->all())
                ->post('api/auth');

            $user = JWTAuth::toUser($token['token']);
            Auth::login($user, true);
			
        } catch (ResourceException $e) {
        	
            Log::info("CSSI App Login Failure");
			Log::info(json_encode($e->getErrors()));
        	
			return Response::json(array(
						            'status' => 0,
						            'message' => "CSSI App Login Failure.",
									'errors' => $e->getErrors()),
						            200
						          );
				
        } catch (InternalHttpException $e) {
        	
			Log::info("CSSI App Login Failure: Wrong email or password.");
			 
			return Response::json(array(
						            'status' => 0,
						            'message' => "CSSI App Login Failure.",
									'errors' => ["Wrong email or password."]),
						            200
						          );

        }

        return Response::json(array(
						            'status' => 1,
						            'message' => "Login Success.",
									'token' => $token,
									'key' => Auth::user()->key),
						            200
						          );
		
	}


    public function passwordForm()
    {
        return view('auth/password');
    }

    public function password(Request $request)
    {
        try {
            $response = $this->api
                ->with($request->all())
                ->post('api/auth/password');

        } catch (ResourceException $e) {
            return back()
                ->withErrors($e->getErrors())
                ->withInput($request->all());
        }

        flash('A link to reset your password has been sent to '.$request->email);
        return redirect(secure_url('login'));
    }


    public function passwordResetForm()
    {
        return view('auth/reset');
    }


    public function passwordReset(Request $request)
    {
        try {
            $response = $this->api
                ->with($request->all())
                ->post('api/auth/password/reset');

        } catch (ResourceException $e) {
            return back()
                ->withErrors($e->getErrors())
                ->withInput($request->all());
        } catch (InternalHttpException $e) {
            flash('Invalid password reset token.');
            return back()
                ->withInput($request->all());
        }


        flash('Your password has been reset');
        return redirect(secure_url('login'));
    }


    public function logout()
    {
        Auth::logout();
        flash('You have been logged out of your account.');
        return redirect(secure_url('login'));
    }

    public function index()
    {
        return view('auth/index');
    }

}