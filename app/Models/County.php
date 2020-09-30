<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model {

	public $timestamps = false;

	public function getStateCodeWithTitleAttribute()
	{
		return $this->state_code.' - '.$this->title;
	}

	public function getTitleWithExtraCostAttribute()
	{
		if ($this->extra_cost > 0) {
			return $this->title . ' + '.displayMoney($this->extra_cost);
		}

		return $this->title;
	}
}