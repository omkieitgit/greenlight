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

class CmaArvValidations
{

    public static function CmaArvAddValidation()
    {
        return $rules = [

            'house_id'                   => 'required|numeric|exists:home_information,house_id',
            'user_id'                    => 'nullable|numeric|exists:users,id',
            'date'                       => 'nullable|date|date_format:"Y-m-d',
            'info_added_by'              => [
                Rule::in(array_keys(config('property_information.info_added_by'))),
            ],
            'specific_demand'            => 'nullable',
            'general_demand'             => 'nullable',
            'days_on_market'             => 'nullable|numeric',
            'phase_renovation'           => 'nullable',
            'price_sqft_sale_comps_from' => 'nullable',
            'price_sqft_sale_comps_to'   => 'nullable',
            'ssd_sale_comps'             => 'nullable',
            'gsd_sale_comps'             => 'nullable',
            'rent_gsd'                   => 'nullable',
            'price_sqft_sold_comps_from' => 'nullable',
            'price_sqft_sold_comps_to'   => 'nullable',
            'ssd_sold_comps'             => 'nullable',
            'gsd_sold_comps'             => 'nullable',
            'rental_comps_map'           => 'nullable',
            'p1_value'                   => 'nullable',
            'p2_value'                   => 'nullable',
            'p3_value'                   => 'nullable',
            'rents_zestimate'            => 'nullable',
            'p1_adom'                    => 'nullable',
            'p2_adom'                    => 'nullable',
            'p3_adom'                    => 'nullable',
            'rental_rate'                => 'nullable',
            'comp_url_1'                 => 'nullable',
            'comp_url_2'                 => 'nullable',
            'comp_url_3'                 => 'nullable',
            'comp_url_4'                 => 'nullable',
            'recommended_cma_arv'        => 'nullable',
            'wholetail_value'            => 'nullable',
        ];
    }

    public static function CmaArvUpdateValidation()
    {
        return $rules = [
            'date'                       => 'nullable|date|date_format:"Y-m-d',
            'info_added_by'              => [
                'required',
                Rule::in(array_keys(config('property_information.info_added_by'))),
            ],
            'user_id'                    => 'nullable|numeric|exists:users,id',
            'specific_demand'            => 'nullable',
            'general_demand'             => 'nullable',
            'days_on_market'             => 'nullable|numeric',
            'phase_renovation'           => 'nullable',
            'price_sqft_sale_comps_from' => 'nullable|numeric|max:9999999999.99',
            'price_sqft_sale_comps_to'   => 'nullable|numeric|max:9999999999.99',
            'ssd_sale_comps'             => 'nullable',
            'gsd_sale_comps'             => 'nullable',
            'rent_gsd'                   => 'nullable',
            'price_sqft_sold_comps_from' => 'nullable|numeric|max:9999999999.99',
            'price_sqft_sold_comps_to'   => 'nullable|numeric|max:9999999999.99',
            'ssd_sold_comps'             => 'nullable',
            'gsd_sold_comps'             => 'nullable',
            'rental_comps_map'           => 'nullable',
            'p1_value'                   => 'nullable|numeric|max:9999999999.99',
            'p2_value'                   => 'nullable|numeric|max:9999999999.99',
            'p3_value'                   => 'nullable|numeric|max:9999999999.99',
            'rents_zestimate'            => 'nullable',
            'p1_adom'                    => 'nullable|numeric|max:9999999999.99',
            'p2_adom'                    => 'nullable|numeric|max:9999999999.99',
            'p3_adom'                    => 'nullable|numeric|max:9999999999.99',
            'rental_rate'                => 'nullable|numeric|max:9999999999.99',
            'comp_url_1'                 => 'nullable',
            'comp_url_2'                 => 'nullable',
            'comp_url_3'                 => 'nullable',
            'comp_url_4'                 => 'nullable',
            'recommended_cma_arv'        => 'nullable|numeric|max:9999999999.99',
            'wholetail_value'            => 'nullable|numeric|max:9999999999.99',
        ];
    }

    public static function CmaArvAddSingleValidation()
    {
        return $rules = [

            'house_id'                   => 'required|numeric|exists:home_information,house_id',
            'user_id'                    => 'nullable|numeric|exists:users,id',
            'info_added_by'              => [
                Rule::in(array_keys(config('property_information.info_added_by'))),
            ]
        ];
    }
    
}
