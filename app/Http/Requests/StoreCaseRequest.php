<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'name_type' => 'required|in:real,alias',

            'location' => 'required|string|max:255',

            'violation_type' => 'required|string|max:100',
            'threat_description' => 'required|string|min:20',

            'case_sensitivity' => 'required|in:low,medium,high',
            'is_pattern_case' => 'required|boolean',

            'birth_date' => 'nullable|date',
            'component' => 'required|string|in:ALAWITE,SUNNI,SHIA,ISMAILI,DRUZE,MURSHIDI,CHRISTIAN,KURD,TURKMEN,CIRCASSIAN,ARMENIAN,ASSYRIAN_CHALDEAN,OTHER',
            'direct_threat' => 'required|boolean',

            'phone' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',

            'psychological_impact' => 'nullable|boolean',
            'impact_details' => 'nullable|string',

            'documents.*' => 'nullable|file|max:10240', // 10MB
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => __('case.full_name_required'),
            'name_type.required' => __('case.name_type_required'),
            'location.required' => __('case.location_required'),
            'violation_type.required' => __('case.violation_type_required'),
            'threat_description.required' => __('case.threat_description_required'),
            'threat_description.min' => __('case.threat_description_min'),
            'case_sensitivity.required' => __('case.case_sensitivity_required'),
            'documents.*.max' => __('case.documents_max'),
            'email.required' => __('case.email_required'),
        ];
    }
}
