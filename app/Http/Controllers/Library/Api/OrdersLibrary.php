<?php

namespace App\Http\Controllers\Library\Api;

use Illuminate\Http\Request;
use App\Http\Requests;

use DB;



class OrdersLibrary{
	
	public function idIsDistinct($id){
		
		$distinct = DB::table("check_distinct")->where('token', $id)->first();
		return is_null($distinct);
	}
	
	public function createOrder($request){
		
		$order = new \App\_Models\Order();
		
		$order->company_id = auth()->user()->company_id;
		$order->user_id = auth()->user()->id;
		
		$order->first_name = $check->first_name;
		$order->middle_name = $check->middle_name;
		$order->last_name = $check->last_name;
		$order->reference_id = createSeed(12);
		$order->sandbox = $order->company->sandbox;
		
		$order->save();
		
		return $order;
		
		//$o->original_id = $check->id;
		//$o->company_id = $check->company_id;
		//$o->user_id = $check->user_id;
		//$o->first_name = $check->first_name;
		//$o->middle_name = $check->middle_name;
		//$o->last_name = $check->last_name;
		
		//$o->created_at = $check->created_at;
		//$o->updated_at = $check->updated_at;
		
		//$o->completed_at = $check->completed_at;
		
		//$o->reference_id = $check->provider_reference_id;
		//$o->sandbox = $check->sandbox;
		
	}
	
}