<?php

namespace App\Http\Controllers\Api\V1;

// Models
use App\Models\User;

// Transformers
use App\Transformers\Api\V1\UserTransformer;


// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Other
use Mail;
use Cache;
use DB;
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class AccountController extends Controller
{
    public function restore(Request $request)
    {
        $this->user()->save();

        return $this->response->item($this->user(), new UserTransformer);
    }

}

