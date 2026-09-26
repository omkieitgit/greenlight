<?php
/**
 * Created By Rativardhan Singh Sengar  6/17/19 12:30 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/26/18 1:58 PM
 */

namespace App\Http\Validations;

use Validator;
use Illuminate\Validation\Rule;

Use Log;

class GeoValidations
{

    public static function updateOrCreateValidations()
    {

        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'latitude'   => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
        ];
    }
}
