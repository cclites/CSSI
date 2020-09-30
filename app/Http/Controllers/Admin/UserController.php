<?php

namespace App\Http\Controllers\Admin;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;

use Spatie\Permission\Models\Role;

use Log;
use Auth;
use Mail;
use App\Models\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
    	
		Log::info("Getting all of the users for admin");

        $users = $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->get('api/admin/users');
		
        if($request->ajax()){
        	
			Log::info("Returning users in the ajax request");
			
            $users = $users->toArray();
            return json_encode($users);
        }

        return view('admin/users/index')
            ->with('users', $users)
            ->with('request', $request->all());
    }

    public function show($id)
    {
        $user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id);

        return view('admin/users/show')
            ->with('user', $user);
    }

    public function approve($id)
    {
    	
		Log::info("Admin/UserController::approve");
		
        $user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/approve');

        flash($user->full_name.'\'s account has been approved.')->important();
        return back();
    }

    public function disapprove($id)
    {
    	Log::info("Admin/UserController::disapprove");
		
        $user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/disapprove');
			
        return back();
    }

    public function promote($id)
    {
        $user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/promote');

        flash($user->full_name.'\'s account has been promoted to Admin.')->important();
        
        return redirect( secure_url('admin/users/'.$user->id) );
    }

    public function demote($id)
    {
        $user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/demote');

        return redirect( secure_url('admin/users/'.$user->id) );
    }
	
	public function apiuser($id){
		$user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/apiuser');

        return redirect( secure_url('admin/users/'.$user->id) );
	}
	
	public function notapiuser($id){
		$user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/notapiuser');

        return redirect( secure_url('admin/users/'.$user->id) );
	}
	
	public function authorizedrep($id){

		$user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/authorizedrep');


        return redirect( secure_url('admin/users/'.$user->id) );
	}
	
	public function notauthorizedrep($id){
		
		$user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/notauthorizedrep');

        return redirect( secure_url('admin/users/'.$user->id) );
	}
	
	public function limitedadmin($id){

		$user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/limitedadmin');


        return redirect( secure_url('admin/users/'.$user->id) );
	}
	
	public function notlimitedadmin($id){

		$user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/notlimitedadmin');


        return redirect( secure_url('admin/users/'.$user->id) );
	}


    public function delete($id)
    {
        $user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id.'/delete');

        return redirect( secure_url('admin/users') );
    }
	
	public function company(Request $request){
		
		$users = $this->api
            ->be(auth()->user())
            ->get('api/users/company/' . auth()->user()->company_id);
		

		return view("authedUsers/index")
					->with('users', $users)
            		->with('request', $request->all());

	}

    public function disguise($id)
    {
        $user = $this->api
            ->be(auth()->user())
            ->get('api/admin/users/'.$id);

        session()->flush();
        session([
            'disguised_user_id' => Auth::id(),
        ]);

        Auth::login($user);

        flash('You\'re now disguised as '.$user->full_name )->important();
        return redirect( secure_url('/') );
    }

    public function createContact(Request $request){
    	
		$email = $request->email;
		
    }

}