<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;

// Models
use App\Models\User;
use App\Models\Check;
use App\Models\Checktype;
use App\Models\Type;
use App\Models\Report;
use App\Models\Price;


use DB;
use Log;

use Carbon\Carbon;
use Exception;


/*
 * Script to manually add price to users.
 * 
 * This is run manually after a type has been added.
 */
class AddPricesForUsers extends Command{
	
	protected $signature = 'add-price';
	protected $description = "Script to add prices for users when new checks are added.";
	
	
	/**
     * Create a new command instance.
     *
     * @return void
     */
	public function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * Main Process
	 * 
	 * @return void
	 */
	public function handle(){
		
		$users = User::all();
		$this->info(count($users));
		
		foreach ($users as $user) {
			$this->addToPricesForUser($user->id);
		}
		
	}
	
    /**
	 * Create price objects for user
	 * 
	 * @return void
	 */
	function addToPricesForUser($id){
		
		
		$types = DB::table('types')->where('id', 14)->get();
		
		foreach($types as $type){
			
			$price = new Price();
			$price->user_id = $id;
			$price->type_id = $type->id;
			$price->amount = $type->default_price;
			$price->save();

		}
	
		
	}
}