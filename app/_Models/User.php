<?php

namespace App\_Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

use Log;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        
    ];

    protected $fillable = [
        'id'
    ];

    // Attributes
    public function getFullNameAttribute()
    {
        $name = ucfirst($this->first_name);
        
        if ($name AND $this->last_name) {
            $name .= ' ';
        }

        $name .= ucfirst($this->last_name);

        return $name;
    }
	
	public function company(){
		return $this->belongsTo("App\_Models\Company");
	}

}
