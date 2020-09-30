<?php

 

    /*
	 * Converts check form old system to
	 */
	 
	/* 
	function convertCheckToOrder($check){
		
		die();
		
		Log::info("conversionTools::convertCheckToOrder");
		//Log::info(json_encode($check));
					
		$o = new \App\_Models\Order;
		
		$o->original_id = $check->id;
		$o->company_id = $check->company_id;
		$o->user_id = $check->user_id;
		$o->first_name = $check->first_name;
		$o->middle_name = $check->middle_name;
		$o->last_name = $check->last_name;
		
		$o->created_at = $check->created_at;
		$o->updated_at = $check->updated_at;
		$o->completed_at = $check->completed_at;
		
		$o->reference_id = createSeed();
		
		$o->save();
	
	}
	 */
	
	/*
	function createChecksFromOrder($order){
		
		Log::info("conversionTools::createChecksFromOrder");
		Log::info(json_encode($order));
		
		$checkTypes = \App\Models\Checktype::where('check_id', $order->original_id)->whereNotNull('completed_at')->get();
		
		foreach($checkTypes as $checkType){
			
			$companyId = $order->company_id;
			
			//echo $companyId . "\n";

			$priceObject = \App\_Models\Price::where('company_id', $companyId)->get();
			$prices = (array)$priceObject;
			

			try{
				
				$amount = $checkType->calculatePrice($priceObject);
				
				$check = new Check();
				
				$check->completed_at = $checkType->completed_at;
				$check->created_at = $checkType->created_at;
			    $check->updated_at = $checkType->updated_at;
				$check->amount = $amount;
				$check->type = $checkType->type_id;
				$check->provider_reference_id = $order->reference_id;
				$check->order_id = $order->id;
								
				$check->save();
				$cnt += 1;
				
			}catch(\Exception $e){
				echo "Tried it\n";
				echo "$companyId\n";
				
				echo json_encode($prices) . "\n";
				
			    $ck = \App\Models\Check::find($checkType->check_id);
				echo json_encode($order) . "\n";
				die();
			}
				
		
			
		}
	}
	 *
	 */


?>