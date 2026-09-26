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

class W9Validations
{

    public static function W9Validation()
    {

        return $rules = [
            'user_id'          => 'required|numeric|exists:tradesman_user,id',
            'name'              => 'nullable',
            'business_name'     => 'nullable',
            'federal_tax'       => 'nullable',
            'address'           => 'nullable',
            'city'              => 'nullable',
            'account_number'    => 'nullable',

            'requesters_name_address'=> 'nullable',
            'exempt_payee_code'=> 'nullable',
            'exemption_FATCA_reporting_code'=> 'nullable',
            'employer_identification_number'=> 'nullable',
            'social_security_number'=> 'nullable',

            'signature'=> 'nullable',
            'w9_date'=> 'nullable|date_format:"Y-m-d',

        ];
    }



    public static function form1099MiscValidation()
    {

        return $rules = [
            'house_id'                          => 'required|numeric|exists:home_information,house_id',
            'recipients_tin'                    => 'nullable',
            'recipients_name'                   => 'nullable',
            'recipients_street_address'         => 'nullable',
            'recipients_city'                   => 'nullable',
        ];
    }

    public static function msc1099FormPdfValidation(){
        return $rules = [
            'recipients_id'                     => 'required|numeric|exists:recipient_info,id',
            'payers_id'                         => 'required|numeric|exists:payers_info,id',
            'amount'                            => 'required|nullable',
        ];
    }

}
