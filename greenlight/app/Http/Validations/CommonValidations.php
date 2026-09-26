<?php
/**
 * Modified By Rativardhan Singh Sengar  10/1/18 8:16 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/1/18 8:15 PM
 */

namespace App\Http\Validations;

use Validator;
use Illuminate\Validation\Rule;

Use Log;

class CommonValidations
{

    public static function documentValidation()
    {

        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.property_document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'case_number'   => 'nullable',
            'document_date' => 'nullable|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:50000', # 15 MB approx
        ];
    }
}
