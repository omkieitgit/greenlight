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

class MapVideoValidations
{

    public static function mapValidation()
    {
        return $rules = [

            'house_id'   => 'required|numeric|exists:home_information,house_id',
            'image_url'  => 'nullable',
            'video_url'  => 'nullable',
            'video_type' => 'nullable',
        ];
    }

    public static function pictureAddValidation()
    {
        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'picture_type' => [
                "required",
                Rule::in(array_keys(config('property_information.picture_type'))),
            ],
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'document'      => 'required|mimes:jpeg,png,jpg,gif,svg,mp4|max:30360', # 30 MB approx
        ];
    }
}
