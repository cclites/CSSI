<?php

namespace App\Http\Controllers\Admin;

// Models
use App\Models\User;
use App\Models\Contact;
use App\Models\Check;
use App\Models\Report;


// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use Log;
use Carbon\Carbon;


class ReportController extends Controller{
	
    public function index(){

        return view('admin/reports/index')
        	->with('data', []);
    }
	
	public function show(Request $request){
		
		Log::info("Admin/ReportController::show");

		$report = Report::find($request->reportId);
		$reportData = decrypt($report->report);
		$reportData = json_decode($reportData);
		
		if( is_string($reportData) ){
			$reportData = json_decode($reportData);
		}
		
		return json_encode(['report'=>$reportData]);

	}
	
	public function update(Request $request){
		
		Log::info("Admin/ReportController::update");
		
		$reportId = $request->reportId;
		$typeId = $request->typeId;
		$report = $request->report;
		
		$report = encrypt($report);
		
		return DB::table('report')
		         ->where('id', $reportId)
				 ->update([
				   'report'=>$report
				 ]);

	}
	
	public function checksForDay(Request $request){
		
		Log::info("Made it into the controller");
		
		$data = $this->api
            ->be(auth()->user())
            ->get('api/admin/reports/checksForDay');
			
		return json_encode($data);
	}
	
	public function checksForMonth(Request $request){
		
		$data = $this->api
            ->be(auth()->user())
            ->get('api/admin/reports/checksForMonth');
			
		return json_encode($data);
	}
	
	public function checksForPriorMonth(Request $request){
		
		$data = $this->api
            ->be(auth()->user())
            ->get('api/admin/reports/checksForPriorMonth');
			
		return json_encode($data);
	}
	
	public function checksForYtd(Request $request){
		
		$data = $this->api
            ->be(auth()->user())
            ->get('api/admin/reports/checksForYtd');
			
		return json_encode($data);
	}
	
	
	public function rawtotals(Request $request){
		
		$data = $this->api
            ->be(auth()->user())
            ->get('api/admin/reports/rawtotals');
			
		return json_encode($data);
		
	}
}
