<?php 

namespace App\Transformers\Api\V1;

use \App\Models\Check;

use Log;
use Crypt;

class CheckTransformer extends \League\Fractal\TransformerAbstract {

    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(Check $check)
    {

        return [
            'id' => $check->id,
            'user_id' => $check->user_id,
            'first_name' => $check->first_name,
            'middle_name' => $check->middle_name,
            'last_name' => $check->last_name,
            'completed_at' => $check->completed_at,
            'created_at' => $check->created_at,
            'updated_at' => $check->updated_at,
        ];
    }

}