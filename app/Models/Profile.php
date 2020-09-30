<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

use Illuminate\Http\Request;
use App\Http\Requests;

class Profile extends Model {
	
	public $timestamps = false;

	// Relationships
    public function check()
    {
    	return $this->belongsTo('App\Models\Check');
    }
	
	public function createProfileData($request){
		
		$params = [
		
    		"birthday" => $request->birthday ? databaseDate($request->birthday): null,
			"address" => $request->address ? $request->address : null,
			"address2" => $request->address2 ? $request->address2 : null,
			"phone" => $request->phone ? $request->phone : null,
			"phone2" => $request->phone2 ? $request->phone2 : null,
			"ip" => $request->ip ? $request->ip : null,
			"ssn" => $request->ssn ? $request->ssn : null,
			"license_state_id" => $request->license_state_id ? $request->license_state_id : null,
			"license_number" => $request->license_number ? $request->license_number : null,
			"city" => $request->city ? $request->city : null,
			"state" => $request->state ? $request->state : null,
			"zip" => $request->zip ? $request->zip : null,
			"email" => $request->email ? $request->email : null,
			"range" => $request->range ? $request->range : null,
            "state_tri_eye_state_ids" => $request->state_tri_eye_state_ids ? $request->state_tri_eye_state_ids : null,
		    "county_tri_eye_county_ids" => $request->county_tri_eye_county_ids ? $request->county_tri_eye_county_ids : null,
		    "county_tri_eye_state"  => $request->county_tri_eye_state ? $request->county_tri_eye_state : null,
		    "federal_state_tri_eye_state_ids" => $request->federal_state_tri_eye_state_ids ? $request->federal_state_tri_eye_state_ids : null,
		    "federal_district_tri_eye_district_ids" => $request->federal_district_tri_eye_district_ids ? $request->federal_district_tri_eye_district_ids : null,
		    
		    'college_name'=> $request->education_college_name ? $request->education_college_name : null,
		    'college_city_and_state'=> $request->education_college_address ? $request->education_college_address : null, //(actually address)
		    'college_phone'=> $request->education_college_phone ? $request->education_college_phone : null,
		    'college_city'=> $request->education_college_city ? $request->education_college_city : null,
		    'college_state'=> $request->education_college_state ? $request->education_college_state : null,
		    'college_zip'=> $request->education_college_zip ? $request->education_college_zip : null,
		    'college_years_attended' => $request->education_college_years_attended ? $request->education_college_years_attended : null,
		    'college_is_graduated' => $request->education_college_is_graduated == 1 ? "Yes" : "No",
		    'college_degree_type'=> $request->education_college_degree_type ? $request->education_college_degree_type : null,
		    'college_graduation_year'=> $request->education_college_graduated_year ? $request->education_college_graduated_year : null,
		    
		    'high_school_name'=> $request->education_high_school_name ? $request->education_high_school_name : null,
		    'high_school_city_and_state'=> $request->education_high_school_city_and_state ? $request->education_high_school_city_and_state : null, //(actually address)
		    'high_school_phone'=> $request->education_high_school_phone ? $request->education_high_school_phone : null,
		    'high_school_city'=> $request->education_high_school_city ? $request->education_high_school_city : null,
		    'high_school_state'=> $request->education_high_school_state ? $request->education_high_school_state : null,
		    'high_school_zip'=> $request->education_high_school_zip ? $request->education_high_school_zip : null,
		    'high_school_years_attended' => $request->education_high_school_years_attended ? $request->education_high_school_years_attended : null,
		    'high_school_is_graduated' => $request->education_high_school_is_graduated == 1 ? "Yes" : "No",
		    'high_school_degree_type'=> $request->education_high_school_degree_type ? $request->education_high_school_degree_type : null,
		    'high_school_graduation_year'=> $request->education_high_school_graduation_year ? $request->education_high_school_graduation_year : null,
		    
			'current_employer_name' => $request->employment_current_employer_name ? $request->employment_current_employer_name : null,
			'current_employer_address' => $request->employment_current_employer_address ? $request->employment_current_employer_address : null,
			'current_employer_phone' => $request->employment_current_employer_phone ? $request->employment_current_employer_phone : null,
			'current_job_title' => $request->employment_current_job_title ? $request->employment_current_job_title : null,
			'current_hire_date' => $request->employment_current_hire_date ? $request->employment_current_hire_date : null,
			'current_employer_city' => $request->employment_current_employer_city ? $request->employment_current_employer_city : null,
			'current_employer_state' => $request->employment_current_employer_state ? $request->employment_current_employer_state : null,
			'current_employer_zip' => $request->employment_current_employer_zip ? $request->employment_current_employer_zip : null,
			
			'past_employer_name' => $request->employment_past_employer_name ? $request->employment_past_employer_name : null,
			'past_employer_address' => $request->employment_past_employer_address ? $request->employment_past_employer_address : null,
			'past_employer_phone' => $request->employment_past_employer_phone ? $request->employment_past_employer_phone : null,
			'past_job_title' => $request->employment_past_job_title ? $request->employment_past_job_title : null,
			'past_hire_date' => $request->employment_past_hire_date ? $request->employment_past_hire_date : null,
			'past_end_date' => $request->employment_past_end_date ? $request->employment_past_end_date : null,
			'past_employer_city' => $request->employment_past_employer_city ? $request->employment_past_employer_city : null,
			'past_employer_state' => $request->employment_past_employer_state ? $request->employment_past_employer_state : null,
			'past_employer_zip' => $request->employment_past_employer_zip ? $request->employment_past_employer_zip : null,
			
		];
		
		return $params;
		
	}
	
}