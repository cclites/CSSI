<?php 

namespace App\Transformers\Api\V1;

use \App\Models\County;

class CountyTransformer extends \League\Fractal\TransformerAbstract {

    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(County $county)
    {
        return [
            'id' => $county->id,
            'title' => $county->title,
            'title_with_extra_cost' => $county->title_with_extra_cost,
            'state_code' => $county->state_code,
            'extra_cost' => $county->extra_cost,
        ];
    }

}