<?php

namespace App\_Models;


use Log;

use Illuminate\Database\Eloquent\Model;

class Check extends Model {
	
	protected $table = 'cssi._checks';
	protected $params;
	protected $order;
	protected $typeId;
	
	
	public function __construct($params, $order, $typeId){
        $this->params = $params;
		$this->order = $order;
		$this->typeId = $typeId;
		return $this->createCheck();
    }
	
    public function order(){
    	return $this->belongsTo("App\_Models\Order");
    }
	
	public function report(){
		return $this->hasOne("App\_Models\Report");
	}
	
	public function description(){
		
	}
	
	public function createCheck(){
		
		$params = $this->params;
		$order = $this->order;
		
		$this->provider_reference_id = $order->provider_reference_id . "-" . createSeed(6);
		$this->order_id = $order->id;
		$this->type = $this->typeId;
		
		$this->save();
		return $this;
		
	}

}