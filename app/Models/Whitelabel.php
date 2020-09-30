<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Whitelabel extends Model {


    // Relationships
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

}