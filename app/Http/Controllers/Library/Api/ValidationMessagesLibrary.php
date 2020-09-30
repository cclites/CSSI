<?php

namespace App\Http\Controllers\Library\Api;

use Illuminate\Http\Request;
use App\Http\Requests;

class ValidationMessagesLibrary{
	
	
	public function messages(){
		
		return [
            'state_tri_eye_state_ids.required' => 'You must select at least one state to run the State Tri-Eye Check',
            'state_tri_eye_state_ids.array' => 'The State Tri-Eye Check requires an array of State IDs',
            'state_tri_eye_state_ids.*.exists' => 'The selected State Tri-Eye states must be valid State IDs',

            'county_tri_eye_county_ids.required' => 'You must select at least one county to run the County Tri-Eye Check',
            'county_tri_eye_county_ids.array' => 'The County Tri-Eye Check requires an array of County IDs',
            'county_tri_eye_county_ids.*.exists' => 'The selected County Tri-Eye counties must be valid County IDs',

            'federal_state_tri_eye_state_ids.required' => 'You must select at least one state to run the Federal State Tri-Eye Check',
            'federal_state_tri_eye_state_ids.array' => 'The Federal State Tri-Eye Check requires an array of State IDs',
            'federal_state_tri_eye_state_ids.*.exists' => 'The selected Federal State Tri-Eye states must be valid State IDs',

            'federal_district_tri_eye_district_ids.required' => 'You must select at least one district to run the Federal District Tri-Eye Check',
            'federal_district_tri_eye_district_ids.array' => 'The Federal District Tri-Eye Check requires an array of District IDs',
            'federal_district_tri_eye_district_ids.*.exists' => 'The selected Federal District Tri-Eye districts must be valid District IDs',
            
			'license_state_id.required' => 'You must select at least one state to run the Motor Vehicle Report',
        ];
		
	}
}