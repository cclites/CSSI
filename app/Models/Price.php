<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model {


    // Relationships
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function type()
    {
    	return $this->belongsTo('App\Models\Type');
    }

}