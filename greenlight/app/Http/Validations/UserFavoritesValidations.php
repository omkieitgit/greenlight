<?php
/**
 * Created By Rativardhan Singh Sengar  4/18/19 8:17 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/2/19 9:04 PM
 */

namespace App\Http\Validations;

use Validator;

Use Log;

class UserFavoritesValidations
{

    public static function updateOrCreate()
    {

        return $rules = [
            'house_id'=> 'required|numeric|exists:home_information,house_id',
        ];
    }

    public static function updateOrCreateDriveList()
    {

        return $rules = [
            'house_id'=> 'required|numeric|exists:home_information,house_id',
        ];
    }

}
