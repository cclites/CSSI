<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {

	// Relationships
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function transactions()
    {
    	return $this->hasMany('App\Models\Transaction')
		    ->where('invoice_id', $this->id)
    		->orderBy('date')
    		->orderBy('id');
    }

}