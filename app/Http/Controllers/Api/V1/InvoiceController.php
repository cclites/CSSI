<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\Adjustment;
use App\Models\Invoice;
use App\Models\Price;
use App\Models\Type;
use App\Models\State;
use App\Models\County;
use App\Models\Stat;

// Transformers
use \App\Transformers\Api\V1\InvoiceTransformer;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;


//Facades
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
use Cache;
use DB;

class InvoiceController extends Controller
{
	protected $li;
	
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [

        ]);

        if ($validator->fails()) {
            throw new ResourceException('Could not filter.', $validator->errors());
        }

        $invoices = Invoice::where('user_id', $this->user()->id)
            ->orderBy('invoices.id', 'desc');

        if($request->amount) {
            $invoices = $invoices->where('invoices.amount', $request->amount);
        }

        $invoices = $invoices->paginate(100);

        return $this->response->paginator($invoices, new InvoiceTransformer);
    }

    public function show($id)
    {

		$invoice = Invoice::where('user_id', $this->user()->id)
            ->where('id', $id)
            ->first();

        if (!$invoice) {
            return $this->response->errorNotFound();
        }

        return $this->response->item($invoice, new InvoiceTransformer);
    }
	

	public function export(){
		
		$rl = new \App\Http\Controllers\Library\Api\Admin\ReportsLibrary;
		return $rl->createTransactionsReport($request->all()); 
		
	}

	public function reconcile(Request $request){
		
		$invoiceId = $request->id;
		
		$invoice = Invoice::find($invoiceId);
		
		if(is_null($invoice)){
			return json_encode(['error'=>0, "message"=>"Invalid invoice ID"]);
		}else{
			
			if($invoice->reconciled){
				
				$invoice->reconciled = false;
				$invoice->reconciled_by = "";
				$invoice->reconciled_date = "0000-00-00 00:00:00";
				$invoice->save();
				
			}else{
				$invoice->reconciled = true;
				$invoice->reconciled_by = Auth::user()->first_name . ' ' . Auth::user()->last_name;
				$invoice->reconciled_date = \Carbon\Carbon::now()->format("Y-m-d h:i");
				$invoice->save();
			}
			
			return json_encode($invoice);
		}
	}
	
	public function read($id){
		return Invoice::find($id);
	}
	
	public function regenerate(Request $request){
		
		$this->il = new \App\Http\Controllers\Library\Api\InvoicesLibrary;
		$il = $this->il;
		
		$invoiceId = $request->invoiceId;
		if(!$invoiceId){
			return json_encode(["error"=>"A valid invoice ID is required."]);
		}else{
		    return $il->regenerateInvoice($invoiceId);	
		}
		
	}
	
	public function updateInvoiceAmount(Request $request){
		
		$invoiceId = $request->invoiceId;
		$val = $request->val;
		
		$status = DB::table("invoices")
		    		->where('id', $invoiceId)
					->update(['amount'=>$val]);
					
		return json_encode(['status'=>$status]);
	}
	
	public function updateAdjustmentAmount(Request $request){
		
		$invoiceId = $request->invoiceId;
		$val = $request->val;
		
		$status = DB::table("invoices")
		    		->where('id', $invoiceId)
					->update(['adjustment'=>$val]);
					
		return json_encode(['status'=>$status]);
	}
	
	public function updateMinimumAmount(Request $request){
		
		$invoiceId = $request->invoiceId;
		$val = $request->val;
		
		$status = DB::table("invoices")
		    		->where('id', $invoiceId)
					->update(['minimum'=>$val]);
					
		return json_encode(['status'=>$status]);
	}

	
}

