<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model {

	public $timestamps = false;

	protected $table = 'educations';

	// Relationships
    public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }
}