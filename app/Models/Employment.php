<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employment extends Model {

	public $timestamps = false;

	// Relationships
    public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }
}