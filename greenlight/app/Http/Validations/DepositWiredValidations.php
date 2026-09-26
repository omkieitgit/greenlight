<?php
/**
 * Created By Rativardhan Singh Sengar  2/21/19 12:25 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/19/19 1:04 PM
 */

namespace App\Http\Validations;

use Validator;
use Illuminate\Validation\Rule;

Use Log;

class DepositWiredValidations
{

    public static function createValidation()
    {
        return $rules = [

            'house_id'           => 'required|numeric|exists:home_information,house_id',
            'user_id'            => 'nullable|numeric|exists:users,id',
            'deposit_wired'      => 'nullable|numeric|max:9999999999.99',
            'deposit_wired_date' => 'nullable|date|date_format:"Y-m-d',
            'paid_by'            => 'nullable|email',
            'county_deposit'     => 'boolean',
            'trustee_deposit'    => 'boolean',
        ];

    }

    public static function updateValidation()
    {
        return $rules = [
            'user_id'            => 'nullable|numeric|exists:users,id',
            'deposit_wired'      => 'nullable|numeric|max:9999999999.99',
            'deposit_wired_date' => 'nullable|date|date_format:"Y-m-d',
            'paid_by'            => 'nullable|email',
            'county_deposit'     => 'boolean',
            'trustee_deposit'    => 'boolean',
        ];

    }

}
