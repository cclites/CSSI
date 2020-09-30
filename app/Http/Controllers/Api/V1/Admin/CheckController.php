<?php

namespace App\Http\Controllers\Api\V1\Admin;

// Models
use App\Models\Check;

// Transformers
use \App\Transformers\Api\V1\CheckTransformer;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Illuminate\Support\Collection;

// Jobs
use App\Jobs\NationalTriEyeCheck;
use App\Jobs\NationalSingleEyeCheck;
use App\Jobs\StateTriEyeCheck;
use App\Jobs\CountyTriEyeCheck;
use App\Jobs\FederalNationalTriEyeCheck;
use App\Jobs\FederalStateTriEyeCheck;
use App\Jobs\FederalDistrictTriEyeCheck;
use App\Jobs\EmploymentCheck;
use App\Jobs\EducationCheck;
use App\Jobs\MvrCheck;


// Other
use DB;
use Excel;
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

use Log;


class CheckController extends Controller
{
    public function index(Request $request)
    {
    	Log::info("V1/Admin/CheckController::index");
		
        $validator = Validator::make($request->all(), [

        ]);

        if ($validator->fails()) {
            throw new ResourceException('Could not filter.', $validator->errors());
        }
		
		if(isset($request->type) && gettype($request->type) == 'string'){
			$request->type = json_decode($request->type);
		}

        //If doing a search, we want to see all checks, including bulk CSSI data checks.
        //Otherwise, admins have no interest in seeing bulk requests
        if( isset($request->search) ){
        	$checks = Check::query();
        }else{
        	$checks = Check::hasNoCssiData();
        }
	  
		if( isset($request->type) && !in_array('all', $request->type)){

			$checks->join("report", "report.check_id", "=", "checks.id")
				    ->whereIn("report.check_type", $request->type)
					->select("report.*", "checks.*");
					
		}

		if( isset($request->company_id) && !in_array("null", $request->company_id)){
			
			//Log::info("Getting by company id");
			$checks->where('checks.company_id', $request->company_id);
		}
		
		if( isset($request->company) && !in_array('all', $request->company) ){
			$checks->whereIn('checks.company_id', $request->company);
		}
		
		if( isset($request->after) &&  isset($request->before) && $request->before !== 'null'){
			
			$after = $request->after . " 00:00:00";
			$before = $request->before . " 00:00:00";
			
			$checks->where("created_at", ">=", $after)
				   ->where("created_at", "<", $before);
			
		}else if( isset($request->after) ){
			
			//Log::info("Getting date after");
			
			$day = explode("-", $request->after);
			$after = Carbon::create($day[0], $day[1], $day[2], 0, 0, 0);
			$before = Carbon::create($day[0], $day[1], $day[2], 0, 0, 0);
			
			$checks->where("created_at", ">=", $after);
			
		}else if( isset($request->before) && $request->before !== 'null' ){
			
			//Log::info("Getting before");
			
			$before = explode("-", $request->before);
			$before = Carbon::create($before[0], $before[1], $before[2], 0, 0, 0);
			
			$checks->where("created_at", "<", $before);
		}
		
		$checks = $checks->with('user')
	             ->orderBy('checks.id', 'desc')
	             ->paginate(25);
	 	 	
		return $this->response->paginator($checks, new CheckTransformer);
		
    }

	public function scopeTypeChecks(){
		
	}

    public function show($id)
    {
        $check = Check::where('id', $id)
            ->first();

        if (!$check) {
            return $this->response->errorNotFound();
        }

        return $this->response->item($check, new CheckTransformer);
    }

    //Mark a check as complete
    public function complete($id)
    {
        $check = Check::where('id', $id)
            ->first();

        if (!$check) {
            return $this->response->errorNotFound();
        }

        $check->completed_at = Carbon::now();
        $check->save();

        return $this->response->item($check, new CheckTransformer);
    }


    //mark a check as incomplete
    public function incomplete($id)
    {
        $check = Check::where('id', $id)
            ->first();

        if (!$check) {
            return $this->response->errorNotFound();
        }

        $check->completed_at = null;
        $check->save();

        return $this->response->item($check, new CheckTransformer);
    }

    public function employment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            throw new UpdateResourceFailedException('Could not update.', $validator->errors());
        }

        $check = Check::where('id', $id)
            ->first();

        if (!$check OR !$check->employment) {
            return $this->response->errorNotFound();
        }

        $check->employment->content = $request->content;
        $check->employment->save();

        $checktype = $check->checktypes->where('type_id', 8)->first();
        $checktype->completed_at = Carbon::now();
        $checktype->save();

        return $this->response->item($check, new CheckTransformer);
    }

    public function education(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            throw new UpdateResourceFailedException('Could not update.', $validator->errors());
        }

        $check = Check::where('id', $id)
            ->first();

        if (!$check OR !$check->education) {
            return $this->response->errorNotFound();
        }

        $check->education->content = $request->content;
        $check->education->save();

        $checktype = $check->checktypes->where('type_id', 9)->first();
        $checktype->completed_at = Carbon::now();
        $checktype->save();

        return $this->response->item($check, new CheckTransformer);
    }

    public function delete($id)
    {
        $check = Check::where('id', $id)
            ->first();

        if (!$check) {
            return $this->response->errorNotFound();
        }
		
		DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('check_county')->where('check_id', $check->id)->delete();
        DB::table('check_district')->where('check_id', $check->id)->delete();
        DB::table('check_state')->where('check_id', $check->id)->delete();
        DB::table('check_state_federal')->where('check_id', $check->id)->delete();
        DB::table('check_type')->where('check_id', $check->id)->delete();
        DB::table('educations')->where('check_id', $check->id)->delete();
        DB::table('employments')->where('check_id', $check->id)->delete();
        DB::table('mvrs')->where('check_id', $check->id)->delete();
		
		$transaction = \App\Models\Transaction::where('check_id', $check->id)->first();
		
		if($transaction){
			$transaction->description = $transaction->description . '    Deleted By: ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
			$transaction->amount = 0.00;
			$transaction->save();
		}
		
		DB::table('report')->where('check_id', $check->id)->delete();
		
		
		//TODO: Change this to create a 0 amount transaction.
		$check->completed_at = null;
        $check->active = false;
		$check->transaction_id = "11111111";
		$check->save();
		
		DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return $this->response->item($check, new CheckTransformer);
    }


    public function redo($check_id, $type_id)
    {
        $check = Check::where('id', $check_id)
            ->first();

        if (!$check) {
            return $this->response->errorNotFound();
        }
		
		$cc = new \App\Http\Controllers\Api\V1\CheckController();
		$cc->addToDailies($type_id, $check_id);

        if ($type_id == 1) {
            dispatch(new NationalTriEyeCheck($check))->onConnection('sync');
        }
        if ($type_id == 2) {
            dispatch(new NationalSingleEyeCheck($check))->onConnection('sync');
        }
        if ($type_id == 3) {
            dispatch(new StateTriEyeCheck($check))->onConnection('sync');
        }
        if ($type_id == 4) {
            dispatch(new CountyTriEyeCheck($check))->onConnection('sync');
        }
        if ($type_id == 5) {
            dispatch(new FederalNationalTriEyeCheck($check))->onConnection('sync');
        }
        if ($type_id == 6) {
            dispatch(new FederalStateTriEyeCheck($check))->onConnection('sync');
        }
        if ($type_id == 7) {
            dispatch(new FederalDistrictTriEyeCheck($check))->onConnection('sync');
        }
        if ($type_id == 8) {
            dispatch(new EmploymentCheck($check))->onConnection('sync');
        }
        if ($type_id == 9) {
            dispatch(new EducationCheck($check))->onConnection('sync');
        }
        if ($type_id == 10) {
            dispatch(new MvrCheck($check))->onConnection('sync');
        }
		if ($type_id == 11) {
            dispatch(new Infutor($check))->onConnection('sync');
        }
		if ($type_id == 12) {
            dispatch(new UsInfoSearch($check))->onConnection('sync');
        }
		if ($type_id == 13) {
            dispatch(new InfutorAuto($check))->onConnection('sync');
        }

        return $this->response->item($check, new CheckTransformer);
    }

}

