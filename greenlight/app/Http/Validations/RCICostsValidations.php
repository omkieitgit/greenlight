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

class RCICostsValidations
{

    public static function updateCreateIncidental()
    {
        return $rules = [

            'house_id'          => 'required|numeric|exists:home_information,house_id',
            'title'              => 'required',
            'cost'        => 'nullable|numeric|max:999999999999.99',
            'monthly'        => 'nullable|numeric|max:999999999999.99',
            'weekly'        => 'nullable|numeric|max:999999999999.99',
            'daily'        => 'nullable|numeric|max:999999999999.99',
            'categories' => [
                "required",
                Rule::in(array_keys(config('property_information.non_hud_expenditures_cat'))),
            ],
        ];
    }

    public static function updateCreateCarry()
    {
        return $rules = [

            'house_id'          => 'required|numeric|exists:home_information,house_id',
            'title'              => 'required',
            'cost'        => 'nullable|numeric|max:999999999999.99',
            'monthly'        => 'nullable|numeric|max:999999999999.99',
            'weekly'        => 'nullable|numeric|max:999999999999.99',
            'daily'        => 'nullable|numeric|max:999999999999.99',
            'categories' => [
                "required",
                Rule::in(array_keys(config('property_information.non_hud_expenditures_cat'))),
            ],
        ];
    }

    public static function renovationDocumentValidation()
    {
        return $rules = [
            'house_id'          => 'required|numeric|exists:home_information,house_id',
            'categories' => [
                "required",
                Rule::in(array_keys(config('property_information.non_hud_expenditures_cat'))),
            ],
            'sub_categories' => [
                "required",
                Rule::in(array_keys(config('property_information.non_hud_expenditures_sub_cat'))),
            ],
            'amount'   => 'nullable|numeric|max:9999999999.99',
            'org_name'      => 'required',
            'store_name'    => 'nullable',
            'description'    => 'nullable',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            //'document_renovation_costs'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:25360', # 15 MB approx
        ];

    }

}
