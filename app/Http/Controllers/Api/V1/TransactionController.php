<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\Transaction;

// Transformers
use \App\Transformers\Api\V1\TransactionTransformer;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;


use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;




class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [

        ]);

        if ($validator->fails()) {
            throw new ResourceException('Could not filter.', $validator->errors());
        }

        $transactions = Transaction::where('parent_id', $this->user()->company_id)
            ->orderBy('transactions.id', 'desc');


        if($request->description) {
            $transactions = $transactions->where('transactions.description', 'like', '%'.$request->description.'%');
        }

        if($request->date) {
            $transactions = $transactions->whereBetween('transactions.created_at', [timetoUTC($request->date), timeToUTC($request->date)->addDays(1)]);
        }

        if($request->min_date) {
            $transactions = $transactions->where('transactions.created_at', '>=', timeToUTC($request->min_date));
        }

        if($request->max_date) {
            $transactions = $transactions->where('transactions.created_at', '<=', timeToUTC($request->max_date)->addDays(1));
        }

        if($request->stripe_charge) {
            $transactions = $transactions->where('transactions.stripe_charge', $request->stripe_charge);
        }

        if($request->amount) {
            $transactions = $transactions->where('transactions.amount', $request->amount);
        }

        $transactions = $transactions->paginate(100);

        return $this->response->paginator($transactions, new TransactionTransformer);
    }

    public function balance()
    {
        $balance = Transaction::where('parent_id', $this->user()->company_id)
            ->sum('amount');

        return $this->response->array([
            'balance' => $balance
        ]);
    }

    public function show($id)
    {
        $transaction = Transaction::where('user_id', $this->user()->id)
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            return $this->response->errorNotFound();
        }

        return $this->response->item($transaction, new TransactionTransformer);
    }
	
	
	public function adminShow($id){
		$transaction = Transaction::where('id', $id)
            ->first();

        if (!$transaction) {
            return $this->response->errorNotFound();
        }

        return $this->response->item($transaction, new TransactionTransformer);
	}
	
	public function update(Request $request){
		
		$id = (double)$request->id;
		
		$transaction = Transaction::find($id);

		if(isset($request->amount)){
			$transaction->amount = $request->amount;
		}
		
		if(isset($request->description)){
			$transaction->description = $request->description;
		}
		
		if(isset($request->notes)){
			$transaction->notes = $request->notes;
		}
		
		$transaction->save();
		
		$il = new \App\Http\Controllers\Library\Api\InvoicesLibrary;
		$il->regenerateInvoice($transaction->invoice_id);
		
		return json_encode($transaction);
		 
	}
	
	
}

