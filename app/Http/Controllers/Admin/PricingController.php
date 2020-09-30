<?php

namespace App\Http\Controllers\Admin;


use App\Models\State;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PricingController extends Controller{
	
	
	public function index(){
		
		return view('admin/pricing/index');
		
	}
	
	
}
