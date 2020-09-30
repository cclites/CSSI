<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;

//never used
class DiagnosticController extends Controller
{
    public function index()
    {
        return view('admin.diagnostics.index');
    }

    

    public function exception(Request $request)
    {
        throw new ResourceException;
    }
}
