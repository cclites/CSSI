<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\County;

// Transformers
use App\Transformers\Api\V1\CountyTransformer;


// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Other
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class CountyController extends Controller
{
	/*
	 * Returns counties for selected states
	 */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [

        ]);

        if ($validator->fails()) {
            throw new ResourceException('Could not filter.', $validator->errors());
        }

        $counties = County::query();

        if($request->search) {
            foreach (str_getcsv($request->search, ' ') as $term) {
                $counties = $counties->where(function($query) use ($term){
                    $query->where('counties.title', 'like', '%'.$term.'%');
                });
            }
        }

        if ($request->state_code) {
            $counties = $counties->where('counties.state_code', $request->state_code);
        }


        $counties = $counties
            ->orderBy('state_code')
            ->orderBy('title')
            ->paginate(1000);

        return $this->response->paginator($counties, new CountyTransformer);
    }

}

