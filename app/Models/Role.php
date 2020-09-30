<?php

namespace App\Models;

use App\Models\Report;
use App\Models\Price;
use App\models\State;

use Log;
use Illuminate\Database\Eloquent\Model;
use Crypt;


class Role extends Model {
	
	protected $table = 'model_has_roles';
}