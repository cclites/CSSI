<?php

namespace App\Models;

use App\Models\Report;
use App\Models\Price;
use App\Models\State;
use App\Models\Profile;
//use App\Models\MvrState;

use Log;
use Parser;
use DB;
use Illuminate\Database\Eloquent\Model;
//use App\Models\Builder;
use Crypt;


class Check extends Model {
	
	protected $fillable = [
	      'id', 
	      'user_id',
	      //'transaction_id',
	      'first_name',
	      'middle_name',
	      'last_name',
	      'birthday',
	      //'completed_at',
	      'created_at',
	      'updated_at',
	      'provider_reference_id',
	      'sandbox',
	      'active'
	];
	

    protected $dates = [
        'birthday',
        'completed_at',
    ];

    // Relationships
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function education()
    {
        return $this->hasOne('App\Models\Education');
    }

    public function employment()
    {
        return $this->hasOne('App\Models\Employment');
    }

	public function report(){
		$reports = $this->hasMany('App\Models\Report')
				   ->where("check_id", $this->id)
				   ->get();
				   
	    return $reports;
	
    }

    //SSNs are not stored in the request
	public function ssn(){
		//return Crypt::decrypt($this->ssn);
		return;
	}
	
	public function providerReference(){
		return $this->provider_reference_id;
	}

    public function types()
    {
        return $this->belongsToMany('App\Models\Type', 'check_type', 'check_id', 'type_id')->withPivot('completed_at');
    }
	
	public function id(){
		return $this->id;
	}

    public function checktypes()
    {
        return $this->hasMany('App\Models\Checktype');
    }

	public function transactions(){
		return $this->hasOne('App\Models\Transaction');
	}
	
    public function counties()
    {
        return $this->belongsToMany('App\Models\County', 'check_county', 'check_id', 'county_id');
    }

    public function districts()
    {
        return $this->belongsToMany('App\Models\District', 'check_district', 'check_id', 'district_id');
    }

    public function states()
    {
        return $this->belongsToMany('App\Models\State', 'check_state', 'check_id', 'state_id');
    }

    public function federal_states()
    {
        return $this->belongsToMany('App\Models\State', 'check_state_federal', 'check_id', 'state_id');
    }
	
	public function mvr_states(){
		return $this->belongsToMany('App\Models\MvrState', 'check_state_mvr', 'check_id', 'state_id');
	}

    // Attributes
    public function getFullNameAttribute()
    {
        $name = ucfirst($this->first_name);
        
        if ($name AND ($this->last_name OR $this->middle_name)) {
            $name .= ' ';
        }

        $name .= ucfirst($this->middle_name);

        if ($name AND $this->last_name) {
            $name .= ' ';
        }

        $name .= ucfirst($this->last_name);

        return $name;
    }

    public function is_completed()
    {
    	Log::info("Setting Check completed");
		
		//How will I bill the check if it isn't complete?
		
        if ($this->checktypes->where('completed_at', NULL)->count() == 0 ) {
            $this->completed_at = \Carbon\Carbon::now();
            //$this->save();
            return true;
        }

        return false;
    }


	public function profile(){
		return $this->hasOne("App\Models\Profile");
	}

    public function company(){
    	return $this->hasOne("App\Models\Company");
    }
	
	public static function scopeHasCssiData($query){
		
		$idsArray = [11,12,13];
		
		return $query->whereHas('types', function($q) use ($idsArray) {
			$q->whereIn('type_id', $idsArray);
		});
		
	}
	
	public static function scopeHasNoCssiData($query){
		
		$idsArray = [11,12,13];
		
		return $query->whereHas('types', function($q) use ($idsArray) {
			$q->whereNotIn('type_id', $idsArray);
		});
		
	}

}