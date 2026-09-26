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

class OwnerBorrowerValidations
{

    public static function ownerCreateValidation()
    {
        return $rules = [

            'house_id'           => 'required|numeric|exists:home_information,house_id',
            'full_name'          => 'nullable',
            'full_address'       => 'nullable',
            'email'              => 'nullable',
            'phone'              => 'nullable',
            'phone2'             => 'nullable',
            'deed_bp_instrument' => 'nullable',
            'deed_recorded_date' => 'nullable|date|date_format:"Y-m-d',
            'beenverified_url'   => 'nullable',

        ];
    }

    public static function ownerUpdateValidation()
    {
        return $rules = [

            'full_name'          => 'nullable',
            'full_address'       => 'nullable',
            'email'              => 'nullable',
            'phone'              => 'nullable|max:11',
            'phone2'             => 'nullable|max:11',
            'deed_bp_instrument' => 'nullable',
            'deed_recorded_date' => 'nullable|date|date_format:"Y-m-d',
            'beenverified_url'   => 'nullable',

        ];
    }

    public static function ownerDocumentValidation()
    {
        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.owner_document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'case_number'   => 'nullable',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }

    public static function borrowerDocumentValidation()
    {
        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.borrower_document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'case_number'   => 'nullable',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }

    public static function borrowerCreateValidation()
    {
        return $rules = [

            'house_id'     => 'required|numeric|exists:home_information,house_id',
            'full_name'    => 'nullable',
            'full_address' => 'nullable',
            'email'        => 'nullable',
                         'phone'        => 'nullable',
            'phone2'       => 'nullable',

        ];
    }

    public static function borrowerUpdateValidation()
    {
        return $rules = [

            'full_name'    => 'nullable',
            'full_address' => 'nullable',
            'email'        => 'nullable',
            'phone'        => 'nullable',
            'phone2'       => 'nullable',

        ];
    }
}
