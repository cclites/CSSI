<?php

namespace App\Http\Controllers\Api\V1\Admin;

// Models
use App\Models\User;
use App\Models\Company;


// Transformers
use \App\Transformers\Api\V1\UserTransformer;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;

use Log;
use DB;
use Hash;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;



/*
 * Mostly used to set user permissions.
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
    	
		Log::info("GETTING USERS IN API/V1");
		
        $validator = Validator::make($request->all(), [

        ]);

        if ($validator->fails()) {
            throw new ResourceException('Could not filter.', $validator->errors());
        }

        $users = User::query();

        if($request->q) {
            foreach (str_getcsv($request->q, ' ') as $term) {
                $users = $users->where(function($query) use ($term){
                    $query->where('users.company_name', 'like', '%'.$term.'%')
                    ->orWhere('users.first_name', 'like', '%'.$term.'%')
                    ->orWhere('users.last_name', 'like', '%'.$term.'%')
                    ->orWhere('users.email', 'like',  '%'.$term.'%')
                    ->orWhere(function ($query) use ($term) {
                        if (stripNonNumeric($term)) {
                            $query->where('users.phone', 'like', '%'.stripNonNumeric($term).'%');
                        }
                        else {
                            $query->where('users.phone', 'like', '%'.$term.'%');
                        }
                    });
                });
            }
        }

        if($request->id) {
            $users = $users->where('id', $request->id);
        }
        if($request->first_name) {
            $users = $users->where('first_name', 'like', '%'.$request->first_name.'%');
        }
        if($request->last_name) {
            $users = $users->where('last_name', 'like', '%'.$request->last_name.'%');
        }
        if($request->email) {
            $users = $users->where('email', 'like', '%'.$request->email.'%');
        }
	
        
        $users = $users
                 ->orderBy('company_name', 'ASC')
                 ->paginate(400);
			
        return $this->response->paginator($users, new UserTransformer);
    }

    public function show($id) {
        $user = User::where('id', $id)
                ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }

        return $this->response->item($user, new UserTransformer);
    }

    public function approve($id) {
    	
		Log::info("V1/admin/UserController::approve");
		
        $user = User::where('id', $id)
            ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }

        $user->is_approved = 1;
        $user->save();

        return $this->response->item($user, new UserTransformer);
    }

    public function disapprove($id) {
    	
		Log::info("V1/admin/UserController::approve");
		
        $user = User::where('id', $id)
            ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }

        $user->is_approved = 0;
        $user->save();

        return $this->response->item($user, new UserTransformer);
    }


    public function promote($id) {
        $user = User::where('id', $id)
            ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }

        $user->assignRole('admin');

        return $this->response->item($user, new UserTransformer);
    }

    public function demote($id) {
        $user = User::where('id', $id)
            ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }

        $user->removeRole('admin');

        return $this->response->item($user, new UserTransformer);
    }
	
	public function apiuser($id){
		
		$user = User::where('id', $id)
            ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }
		
		$user->assignRole("apiauth");
		$user->sandbox=false;
		$user->whitelabel_id = 4; //this is the id of the cssi data whitelabel entry in the DB
								  //This should be in a config somewhere
		$user->save();
		
		return $this->response->item($user, new UserTransformer);
	}

	
	public function notapiuser($id){
		$user = User::where('id', $id)
            ->first();
			
		$user->removeRole("apiauth");
		$user->sandbox=false;
		$user->save();
		
		return $this->response->item($user, new UserTransformer);

	}
	
	public function limitedadmin($id){
		
		$user = User::where('id', $id)
            ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }
		
		$user->assignRole("limited_admin");
		$user->save();
		
		return $this->response->item($user, new UserTransformer);
	}

	
	public function notlimitedadmin($id){
		$user = User::where('id', $id)
            ->first();
			
		$user->removeRole("limited_admin");
		$user->save();
		
		return $this->response->item($user, new UserTransformer);
	}
	
	public function authorizedrep($id){
		
		$user = User::where('id', $id)
            ->first();
		
		if (!$user) {
            return $this->response->errorNotFound();
        }	
			
		$user->company_rep = true;
		$user->save();
		
		return $this->response->item($user, new UserTransformer);
	
	}
	
	public function notauthorizedrep($id){
		
		$user = User::where('id', $id)
            ->first();
		
		if (!$user) {
            return $this->response->errorNotFound();
        }	
			
		$user->company_rep = false;
		$user->save();
		
		return $this->response->item($user, new UserTransformer);
	
	}
	
	public function company($id){
		
		$users = User::where("company_id", $id)->get();
		
		if (!$users) {
            return $this->response->errorNotFound();
        }
		
		return $this->response->item($users, new UserTransformer);
		
	}
	
	public function companyExport(){
		
		$companies = Company::companies();
		
		$report = "Company,Company ID,Main Contact,Email,Phone,Address,City,State,Zip\n";
		
		foreach($companies as $company){
			
			$report .= '"' . $company->data->company_name . '",' . $company->data->company_id . ','
			        . '"' . $company->data->first_name . $company->data->last_name . '",'
			        . $company->data->email . ',' . $company->data->phone . ','
			        . '"' . $company->data->address . " " . $company->data->secondary_address . '",'
			        . $company->data->city . ',' . $company->data->state . ',' . $company->data->zip 
			        . "\n";

            /*
			$report .= "\"" . $company->data->company_name . "\"," . $company->data->company_id . "," 
				    . "\"" . $company->data->first_name . " " . "\"" . $company->data->last_name . "," 
				    . $company->data->email . "," . $company->data->phone . ","
				    . "\"" . $company->data->address . " " . $company->data->secondary_address . "\","
				    . "\"" . $company->data->city . "\"," . $company->data->state . "," . $company->data->zip 
				    . "\n";
			 * *
			 */
		}
		
		return json_encode(['report'=>$report]);
		
	}
	
	public function export($id){
		
		Log::info("Company id is $id");
		
		$company = new Company;
		$company->company_id = $id;		
		$owner = $company->owner();

		$report = "Company,Company ID,Main Contact,Email,Phone,Address,City,State,Zip\n";
		
		$report .= "\"" . $owner->company_name . "\"," . $owner->company_id . "," 
			    . "\"" . $owner->first_name . " " . "\"" . $owner->last_name . "\"," 
			    . $owner->email . "," . $owner->phone . ","
			    . "\"" . $owner->address . "\"\"" . $owner->secondary_address . "\","
			    . "\"" . $owner->city . "\"," . $owner->state . "," . $owner->zip . "\","
			    . "\n\n\n";
				
				
		$report .= "AUTHORIZED USERS\n\n";
		
		$report .= "Name,Email,Phone,Status\n";

		foreach($company->members() as $member){
			
			//Log::info(json_encode($member));
			$report .= "\"" . $member->first_name . " " . $member->last_name . "\","
					. $member->email . "," . $member->phone . ",";
					//. $member->is_suspended ? "Inactive" : "Active" . "\n";
					
			$report .= $member->is_suspended ? "Inactive" : "Active" . "\n";
		}
		
		return json_encode(['report'=>$report]);
		
	}

	public function usersExport(Request $request){
		
		//$users = User::all()->orderBy("last_name")->get();
		$users = DB::table("users")->orderBy("last_name")->get();
		
		$report = "Name,Company Name,Email,Phone, Extension, Cell\n";
		
		foreach($users as $user){
			$report .= $user->first_name . " " . $user->last_name . ",\"" . $user->company_name . "\"," . $user->email . "," . $user->phone . "," . $user->extension . "," . $user->cell_phone . "\n"; 
		}
		
		return json_encode(['report'=>$report]);
	}

    public function delete($id) {
        $user = User::where('id', $id)
            ->first();

        if (!$user) {
            return $this->response->errorNotFound();
        }

        // Delete existing records
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('check_county')
            ->join('checks', 'check_county.check_id', '=', 'checks.id')
            ->where('checks.user_id', $user->id)
            ->delete();
        DB::table('check_district')
            ->join('checks', 'check_district.check_id', '=', 'checks.id')
            ->where('checks.user_id', $user->id)
            ->delete();
        DB::table('check_state')
            ->join('checks', 'check_state.check_id', '=', 'checks.id')
            ->where('checks.user_id', $user->id)
            ->delete();
        DB::table('check_state_federal')
            ->join('checks', 'check_state_federal.check_id', '=', 'checks.id')
            ->where('checks.user_id', $user->id)
            ->delete();
        DB::table('check_type')
            ->join('checks', 'check_type.check_id', '=', 'checks.id')
            ->where('checks.user_id', $user->id)
            ->delete();
        DB::table('educations')
            ->join('checks', 'educations.check_id', '=', 'checks.id')
            ->where('checks.user_id', $user->id)
            ->delete();
        DB::table('employments')
            ->join('checks', 'employments.check_id', '=', 'checks.id')
            ->where('checks.user_id', $user->id)
            ->delete();
        DB::table('mvrs')
            ->join('checks', 'mvrs.check_id', '=', 'checks.id')
            ->where('checks.user_id', $user->id)
            ->delete();


        DB::table('checks')->where('user_id', $user->id)->delete();
        DB::table('invoices')->where('user_id', $user->id)->delete();
        DB::table('logs')->where('user_id', $user->id)->delete();
        DB::table('model_has_permissions')->where('model_id', $user->id)->where('model_type', 'App\Models\User')->delete();
        DB::table('model_has_roles')->where('model_id', $user->id)->where('model_type', 'App\Models\User')->delete();
        DB::table('prices')->where('user_id', $user->id)->delete();
        DB::table('transactions')->where('user_id', $user->id)->delete();

        DB::table('users')->where('id', $user->id)->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        
        return $this->response->array([
            'user_id' => $id,
        ]);
    }

	public function update(Request $request){
		
		Log::info("***************** UserController::update");
		
		if($request->type == 'tou_reset'){
			
			DB::table('cssi.tou_reset')->where('user_id', $request->id)->first()->delete();
			
		}
		 
		if($request->type == "company_id" && !strlen($request->val) == 6){
			return;
		}
		
		if($request->type == 'phone'){
			$request->val = databasePhone($request->val);
		}
		
		//This updates the user. Need to update the company also.
		$user = User::find($request->id);
		$user[$request->type] = $request->val;
		$user->save();
		
		try{
			
			Log::info($request->type);
			Log::info($request->id);
			
			$company = \App\_Models\Company::where('company_id', $user->company_id)->first();
			$company[$request->type] = $request->val;
			$company->save();
			
		}catch(\Exception $e){
			Log::info(json_encode($e));
		}
			
	}
	
	

}

