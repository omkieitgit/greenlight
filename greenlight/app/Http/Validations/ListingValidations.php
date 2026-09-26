<?php
/**
 * Created By Mranalinee Chouhan
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/1/18 8:15 PM
 */

namespace App\Http\Validations;

use Validator;
use Illuminate\Validation\Rule;

Use Log;

class ListingValidations
{

    public static function documentValidation()
    {

        return $rules = [
            'house_id'              => 'required|numeric|exists:home_information,house_id',
            'document_type'         => [ "required"
            ],
            'document_date'         => 'nullable|date_format:"Y-m-d',
            'document'              => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }


}
