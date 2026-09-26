<?php
/**
 * Created By Rativardhan Singh Sengar  4/21/19 3:50 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/18/19 8:18 PM
 */

namespace App\Http\Validations;

use Validator;

Use Log;

class PropertyQueueListHousesValidations
{

    public static function updateOrCreate()
    {

        return $rules = [
            'house_id'          => 'required|numeric|exists:home_information,house_id',
        ];
    }

}
