<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

// Models
use App\Models\Type;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Recipients\EmployeeRecipient;
use App\Notifications\EmployeeScreenEmail;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Parser;
use App\Http\Requests;
use Illuminate\Http\Request;
use Log;
use DB;


class TypeController extends Controller{
	
	public function toggle(Request $request){
		
		$typeId = $request->typeId;
		
		$type = Type::where('id', $typeId)->first();
		
		Log::info(json_encode($type));
		
		if(!$type->enabled){
			return $this->enableCheckType($typeId);
		}elseif($type->enabled){
			return $this->disableCheckType($typeId);
		}else{
			return json_encode(['status'=>-1]); 
		}
		
	}
		
	public function disableCheckType($typeId){
		DB::table("types")->where('id', $typeId)->update(["enabled"=> false]);
		return json_encode(['status'=>0]);
	}
	
	public function enableCheckType($typeId){
		DB::table("types")->where('id', $typeId)->update(["enabled"=> true]);
		return json_encode(['status'=>0]);
	}

	
}