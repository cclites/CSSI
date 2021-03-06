<?php

namespace App\Http\Controllers\Admin;

// Models
use App\Models\User;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


use Log;
use Auth;
use Parser;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckController extends Controller
{
	public function index(Request $request)
	{
        try {
            $checks = $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->get('api/admin/checks');
        } catch (ResourceException $e) {
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }

		return view('admin/checks/index')
            ->with('request', $request)
            ->with('checks', $checks);
	}


    public function show(Request $request, $id, $view = 'html')
    {
    	Log::info("Admin/CheckController::show()");
		
        $check = $this->api
            ->be(auth()->user())
            ->get('api/admin/checks/'.$id);

        //never used
        if ($view == 'parsed') {
            dd($check->parsed_results);
        }

        //never used
        if ($view == 'standardized') {
            dd($check->standardized_results);
        }
		
        return view('admin/checks/show')
            ->with('check', $check);
    }

    public function complete(Request $request, $id)
    {
        $this->api
            ->be(auth()->user())
            ->get('api/admin/checks/'.$id.'/complete');

        return redirect(secure_url('admin/checks/'.$id ));
    }

    public function incomplete(Request $request, $id)
    {
        $this->api
            ->be(auth()->user())
            ->get('api/admin/checks/'.$id.'/incomplete');

        return redirect(secure_url('admin/checks/'.$id));
    }

    public function employment(Request $request, $id)
    {
        if (!$request->all()) {
            $check = $this->api
                ->be(auth()->user())
                ->get('api/admin/checks/'.$id);

            return view('admin/checks/employment')
                ->with('check', $check);
        }

        try {
            $check = $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->post('api/admin/checks/'.$id.'/employment');
        } catch (ResourceException $e) {
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }

        return redirect(secure_url('admin/checks/'.$id));
    }

    public function education(Request $request, $id)
    {
        if (!$request->all()) {
            $check = $this->api
                ->be(auth()->user())
                ->get('api/admin/checks/'.$id);

            return view('admin/checks/education')
                ->with('check', $check);
        }

        try {
            $check = $this->api
            ->be(auth()->user())
            ->with($request->all())
            ->post('api/admin/checks/'.$id.'/education');
        } catch (ResourceException $e) {
            return back()
            ->withErrors($e->getErrors())
            ->withInput($request->all());
        }

        return redirect('admin/checks/'.$id);
    }

    public function delete(Request $request, $id)
    {
        $this->api
            ->be(auth()->user())
            ->get('api/admin/checks/'.$id.'/delete');

        flash('The check has been deleted');
		
		return redirect( secure_url('admin/checks/') );

    }


    public function redo(Request $request, $check_id, $type_id)
    {
        $this->api
            ->be(auth()->user())
            ->get('api/admin/checks/'.$check_id.'/redo/'.$type_id);

        flash('That portion of the check has been resubmitted');

        return redirect( secure_url('admin/checks/'.$check_id));
    }
	
	
	public function addToDailies($typeId){
    	
		$day = date(“Ymd”);
		
		//see if record exists
		$record = DB::table('_dailies')
				  ->where('type', $typeId)
				  ->where('day', $day)
				  ->get();
				  
		if($record){
			
			DB::table('_dailies')
				->where('type', $typeId)
				->where('day', $day)
				->update(['total'=> $record->total += 1] );
			
		}
    	
    }

}