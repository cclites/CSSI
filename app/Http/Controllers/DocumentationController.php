<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;

//Not used
class DocumentationController extends Controller
{
	public function index()
    {
    	return view('documentation/index');
    }

}

