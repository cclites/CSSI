<?php 

namespace App\Transformers\Api\V1;

use \App\Models\District;

class DistrictTransformer extends \League\Fractal\TransformerAbstract {

    protected $availableIncludes = [

    ];

    protected $defaultIncludes = [

    ];

    public function transform(District $district)
    {
        return [
            'id' => $district->id,
            'state_code' => $district->state_code,
            'title' => $district->title,
        ];
    }

}