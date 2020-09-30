<?php

namespace App\Http\Controllers\Api\V1\Admin;

// Models
use App\Models\User;
use App\Models\Subscription;
use App\Models\Transaction;

// Transformers
use \App\Transformers\Api\V1\TransactionTransformer;
use \App\Transformers\Api\V1\UserTransformer;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;

use Mail;
use DB;
use Hash;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DiagnosticController extends Controller
{
    public function email(Request $request)
    {
        $messages = [
            
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], $messages);

        if ($validator->fails()) {
            throw new ResourceException('Could not send test email.', $validator->errors());
        }

        $email = $request->email;
        Mail::send('emails.test', [], function ($m) use ($email) {
            $m->to($email)->subject('Test Email');
        });

        return $this->response->array([
            'message' => 'Test email send to '.$request->email,
            'status_code' => 200
        ]);
    }


    public function email(Request $request)
    {
        $messages = [
            
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], $messages);

        if ($validator->fails()) {
            throw new ResourceException('Could not send test email.', $validator->errors());
        }

        $email = $request->email;
        Mail::send('emails.test', [], function ($m) use ($email) {
            $m->to($email)->subject('Test Email');
        });

        return $this->response->array([
            'message' => 'Test email send to '.$request->email,
            'status_code' => 200
        ]);
    }
}
