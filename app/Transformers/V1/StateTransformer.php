<?php 

namespace App\Transformers\Api\V1;

use \App\Models\State;

class StateTransformer extends \League\Fractal\TransformerAbstract {

    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(State $state)
    {
        return [
            'id' => $state->id,
            'code' => $state->code,
            'title' => $state->title,
            'extra_cost' => $state->extra_cost,
            'mvr_cost' => $state->mvr_cost,
        ];
    }

}