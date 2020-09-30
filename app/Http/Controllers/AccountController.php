<?php

namespace App\Http\Controllers;

// Models
use App\Models\User;

// Exceptions
use Dingo\Api\Exception\DeleteResourceFailedException;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Auth;
use \Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;

class AccountController extends Controller
{
	public function undisguise()
    {
    	$current_user_id = Auth::id();

        if (!session()->has('disguised_user_id')) {
            flash('You\'re not disguised as anyone else. Try to be happy with being you!');
            return redirect('/');
        }

        $user = User::findOrFail(session('disguised_user_id'));

        session()->flush();

        Auth::login($user);

        flash('You\'re back to your main account.')->important();

        if (Auth::user()->hasRole('admin')) {
            return redirect('/admin/users');
        }
        if (Auth::user()->hasRole('sales')) {
            return redirect('/sales/users');
        }

        return redirect('/');
    }

    public function sidebar()
    {
        Auth::user()->is_sidebar = (Auth::user()->is_sidebar - 1) * -1;
        Auth::user()->save();
    }

    public function restore()
    {
        $user = $this->api
            ->be(auth()->user())
            ->get('api/account/restore');

        flash('Your account has been restored to the default settings')->important();
        return redirect('/');
    }

}

