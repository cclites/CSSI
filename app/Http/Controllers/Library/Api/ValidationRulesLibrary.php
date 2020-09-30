<?php

namespace App\Http\Controllers\Library\Api;

use Illuminate\Http\Request;
use App\Http\Requests;

class ValidationRulesLibrary{
	
	//Required of all checks
	public function basicRules(){
		return [
	            'check_types' => 'required|array',
	            'first_name' => 'required|max:50',
	            'middle_name' => 'max:50',
	            'last_name' => 'required|max:50',
	            'birthday' => 'required|date',
	        ];
	}
	
	public function employmentRules($rules){
		$rules['employment_current_employer_name'] = 'required_without:employment_past_employer_name|max:100';
		//$rules['employment_current_hire_date'] = 'required_with:employment_current_employer_name|date';
		$rules['employment_current_job_title'] = 'required_with:employment_current_employer_name|max:50';
		$rules['employment_current_employer_city'] = 'required_with:employment_current_employer_name|max:50';
		$rules['employment_current_employer_state'] = 'required_with:employment_current_employer_name|max:50';
		$rules['employment_current_employer_zip'] = 'required_with:employment_current_employer_name|max:50';
		$rules['employment_current_employer_phone'] = 'required_with:employment_current_employer_name|max:18';
		$rules['employment_past_employer_name'] = 'required_without:employment_current_employer_name|max:100';
		//$rules['employment_past_hire_date'] = 'required_with:employment_past_employer_name|date';
		//$rules['employment_past_end_date'] = 'required_with:employment_past_employer_name|date';
		$rules['employment_past_job_title'] = 'required_with:employment_past_employer_name|max:50';
		$rules['employment_past_employer_city'] = 'required_with:employment_past_employer_name|max:50';
		$rules['employment_past_employer_state'] = 'required_with:employment_past_employer_name|max:50';
		$rules['employment_past_employer_zip'] = 'required_with:employment_past_employer_name|max:50';
		$rules['employment_past_employer_phone'] = 'required_with:employment_past_employer_name|max:18';
		return $rules;
	}
	
	public function educationRules($rules){
		$rules['education_college_name'] = 'required_without:education_high_school_name|max:132';
		$rules['education_college_is_graduated'] = 'required_with:education_college_name'; //0 or 1
		$rules['education_college_degree_type'] = 'required_with:education_college_is_graduated|max:132';
		$rules['education_college_city'] = 'required_with:education_college_name|max:50';
		$rules['education_college_state'] = 'required_with:education_college_name|max:50';
		$rules['education_college_zip'] = 'required_with:education_college_name|max:50';
		
		$rules['education_high_school_name'] = 'required_without:education_college_name|max:132';
		$rules['education_high_school_is_graduated'] = 'required_with:education_high_school_name'; //0 or 1
		$rules['education_high_school_city'] = 'required_with:education_high_school_name|max:50';
		$rules['education_high_school_state'] = 'required_with:education_high_school_name|max:50';
		return $rules;
	}
	
	public function ssnRules($rules){
		$rules['ssn'] = 'required|ssn';
		return $rules;
	}

	public function stateTriEyeRules($rules){
		$rules['state_tri_eye_state_ids'] = 'required|array';
        $rules['state_tri_eye_state_ids.*'] = 'exists:states,id';
		return $rules;
	}

	public function countyTriEyeRules($rules){
		$rules['county_tri_eye_county_ids'] = 'required|array';
        $rules['county_tri_eye_county_ids.*'] = 'exists:counties,id';
		return $rules;
	}

	public function federalStateTriEyeRules($rules){
		$rules['federal_state_tri_eye_state_ids'] = 'required|array';
        $rules['federal_state_tri_eye_state_ids.*'] = 'exists:states,id';
		return $rules;
	}

	public function federalDistrictRules($rules){
		$rules['federal_district_tri_eye_district_ids'] = 'required|array';
        $rules['federal_district_tri_eye_district_ids.*'] = 'exists:districts,id';
		return $rules;
	}

    public function mvrRules($rules){
		$rules['license_state_id'] = 'required|exists:states,id';
		return $rules;
	}
}
