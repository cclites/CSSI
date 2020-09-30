<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Infutor;
use App\Models\UsInfoSearch;
use App\Models\Checktype;
use App\Http\Controllers\ReportController;

use \Carbon\Carbon;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Http\Request;
use App\Http\Requests;

use Log;
use DB;
use Response;
use Crypt;


//Not used yet. Will be used by the Mobile app
class CssiDataCheck implements ShouldQueue{
	
	protected $infutor;
	protected $usinfo;
	
	public function __construct(Infutor $infutor, UsInfoSearch $usinfo){
        $this->infutor = $infutor;
		$this->usinfor = $usinfo;
    }
	
	public function handle(){
		
		$infutor = $this->infutor;
		$usinfo = $this->usinfo;
	}
	
}