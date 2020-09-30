<?php

namespace App\Http\Controllers\Admin;

// Models
use App\Models\User;
use App\Models\Email;
use App\Models\Text;


use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


// use Goutte\Client;

use \App\Notifications\WelcomeEmail;

// Guzzle
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

use Mail;
use Carbon\Carbon;
use Cache;
use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SandboxController extends Controller
{
    public function index(Request $request)
    {
    	$contents = file_get_contents("https://maps.google.com/?cid=1546593854915219647");

	    preg_match("/(0x[a-z0-9]{4,}:0x[a-z0-9]{4,})/",$contents,$matches);

	    echo $matches[0]; //LRD code

        // $hex = bcdechex('1546593854915219647');

        // exit($hex);
    }
}