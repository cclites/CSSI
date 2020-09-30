<?php

namespace App\Http\Controllers\Api\V2;

// Models
use App\_Models\Check;
use App\_Models\Company;
use App\_Models\Order;
use App\_Models\Price;

use Log;

use App\Http\Requests;
use Illuminate\Http\Request;


class OrderController extends Controller{
	
	protected $il;
	
	public function __construct()
    {
		require_once(__DIR__ . '/../../../vendor/autoload.php');
    }
	
	public function store(Request $request){
		
		//create an order
		
		
	}
	


}