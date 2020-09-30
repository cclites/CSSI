<?php

namespace App\Providers;

// Models
use App\Models\Type;
use App\Models\County;
use App\Models\State;
use App\Models\District;
use App\Models\Whitelabel;
use App\Models\User;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;


use DB;
use Auth;
use Cache;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		//Make sure that env files are up to date
		//Log::info("Checking configs is AppServiceProvider::boot");
    	//checkConfigs();

        // See https://laravel-news.com/laravel-5-4-key-too-long-error
        Schema::defaultStringLength(191);

        $types = Cache::remember('types', 60, function() {
            return Type::orderBy('id')
                ->get();
        });

        $counties = Cache::remember('counties', 60, function() {
            return County::orderBy('state_code')
                ->orderBy('title')
                ->get();
        });

        $districts = Cache::remember('districts', 60, function() {
            return District::orderBy('state_code')
                ->orderBy('title')
                ->get();
        });

        $states = Cache::remember('states', 60, function() {
            return State::orderBy('code')
                ->get();
        });


        $whitelabels = Cache::remember('whitelabels', 60, function() {
            return Whitelabel::orderBy('id')
                ->get();
        });


        //only do this if we are on a CSSI data url        
		$host = request()->getHttpHost();
		
		$whitelabel = null;
		
		if(strpos($host, 'cssidata') !== false){
			$whitelabel =  $whitelabels->where('host', $host)->first();
		}
		
		//Do I have a user object?
		//Log::info("Do I have a user id? " . Auth::user()->first_name);
		
        

        if (!$whitelabel) {
            $whitelabel = $whitelabels->first();
        }
		
        view()->share('whitelabel', $whitelabel);


        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            // note: making sure we have something
            if(!isset($value{9})) { return false; }
            
            // note: strip out everything but numbers
            $value = preg_replace("/[^0-9]/", "", $value);
            
            $length = strlen($value);
            if (strlen($value) == 10) {
                return true;
            }

            if (strlen($value) == 11 AND $value[0] == 1) {
                return true;
            }

            return false;
        }, 'The phone number is invalid');


        Validator::extend('ssn', function ($attribute, $value, $parameters, $validator) {
            // note: making sure we have something
            if(!isset($value{8})) { return false; }
            
            // note: strip out everything but numbers
            $value = preg_replace("/[^0-9]/", "", $value);
            
            $length = strlen($value);
            if (strlen($value) == 9) {
                return true;
            }

            return false;
        }, 'The Social Security Number is invalid');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
