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

class PropertyValidations
{

    public static function createEditPropertyValidation()
    {


        return $rules = [

            'address'                => '',
            'city'                   => '',
            'county'                 => '',
            'state'                  => [ Rule::in(array_keys(config('constants.states')))],
            'zip'                    => 'nullable|string|max:10',
            'total_living_sqft'      => 'nullable|numeric|max:9999999999.99',
            'cost_per_sqft'          => 'nullable|numeric|max:9999999999.99',
            'total_sqft'             => 'nullable|numeric|max:9999999999.99',
            'cost_sqft'              => 'nullable|numeric|max:9999999999.99',
            'main_floor_area'        => 'nullable|numeric|max:9999999999.99',
            'second_floor_area'      => 'nullable|numeric|max:9999999999.99',
            'third_floor_area'       => 'nullable|numeric|max:9999999999.99',
            'basement_area'          => 'nullable|numeric|max:9999999999.99',
            'finished_basement_area' => 'nullable|numeric|max:9999999999.99',
            'finished_attic'         => 'nullable|numeric|max:9999999999.99',
            'enclosed_porch'         => 'nullable',
            'bonus_room'             => 'nullable|integer',
            'year_built'             => 'nullable|integer',
            'bed'                    => 'nullable|numeric',
            'bath'                   => 'nullable|numeric',
            'full_bath'              => 'nullable|integer',
            'half_bath'              => 'nullable|integer',
            'three_quarter_bath'     => 'nullable|integer',
            'of_families'            => 'nullable|integer',
            'of_kitchen'             => 'nullable|integer',
            'fireplaces'             => 'nullable|integer',
            'subdivision'            => 'nullable|',
            'ext_wall_type'          => [
                Rule::in(array_keys(config('property_information.ext_wall_type'))),
            ],
            'roofing'                => [
                Rule::in(array_keys(config('property_information.roofing'))),
            ],
            'ac'                     => [
                Rule::in(array_keys(config('property_information.ac_heating'))),
            ],
            'heating'                => [
                Rule::in(array_keys(config('property_information.ac_heating'))),
            ],
            'pool'                   => [
                Rule::in(array_keys(config('property_information.pool_spa'))),
            ],
            'spa'                    => [
                Rule::in(array_keys(config('property_information.pool_spa'))),
            ],
            'garages'                => 'nullable|integer',
            'garage_types'           => [
                Rule::in(array_keys(config('property_information.garage_types'))),
            ],
            'garage_sf'              => 'nullable|numeric|max:9999999999.99',
            'lot_acreage_sf'         => 'nullable|numeric|max:9999999999.99',
            'stories'                => 'nullable|integer',
            'property_type'          => [
                Rule::in(array_keys(config('property_information.property_types'))),
            ],
            'specific_property_type' => 'nullable|integer',
            'building_style'         => [
                Rule::in(array_keys(config('property_information.building_style'))),
            ],
            'parcel_id1'             => 'nullable|',
            'parcel_id2'             => 'nullable|',
            'prc_url'                => 'nullable|max:255',
            'country_assessor_url'   => 'nullable|max:255',
            'gis_url'                => 'nullable|max:255',
            'treasurer_url'          => 'nullable|max:255',
            'tax_bill_url'           => 'nullable|max:255',
            'record_number'          => 'nullable|',
            'choose_prc'             => 'nullable|',

        ];

    }

    public static function assessmentCreateValidation()
    {
        return $rules = [

            'house_id'            => 'required|numeric|exists:home_information,house_id',
            'taxes_assessed'      => 'nullable|numeric|max:9999999999.99',
            'taxes_year'          => 'nullable|numeric',
            'property_taxes_owed' => 'nullable|numeric|max:9999999999.99',
            'property_taxes_owed_year'          => 'nullable|numeric|max:9999',

        ];

    }

    public static function assessmentUpdateValidation()
    {
        return $rules = [
            'taxes_assessed'      => 'nullable|numeric|max:9999999999.99',
            'taxes_year'          => 'nullable|numeric|max:9999',
            'property_taxes_owed' => 'nullable|numeric|max:9999999999.99',
            'property_taxes_owed_year'          => 'nullable|numeric|max:9999',
        ];

    }

    public static function assessmentUpdateOrCreateAllValidation()
    {
        return $rules = [
            'json'              => [
                'array',
                'min:1"',
            ],

            'json.*.id'              => [
                'nullable',
                'numeric',
                'exists:property_assessment,id',
            ],'json.*.house_id'              => [
                'required',
                'numeric',
                'exists:home_information,house_id',
            ],
            'json.*.taxes_assessed'              => [
                'nullable',
                'numeric',
                'max:9999999999.99',
            ],
            'json.*.taxes_year'              => [
                'nullable',
                'numeric',
                'max:9999',
            ],
            'json.*.property_taxes_owed'              => [
                'nullable',
                'numeric',
                'max:9999999999.99',
            ],
            'json.*.property_taxes_owed_year'              => [
                'nullable',
                'numeric',
                'max:9999',
            ],

        ];

    }


    public static function localRealEstateValidation()
    {
        return $rules = [
            'house_id'         => 'required|numeric|exists:home_information,house_id',
            'zillow_url'       => 'nullable|',
            'zestimate'        => 'nullable|numeric|max:9999999999.99',
            'redfin_url'       => 'nullable|',
            'redfin_est'       => 'nullable|numeric|max:9999999999.99',
            'realtor_url'      => 'nullable|',
            'realtor_est'      => 'nullable|numeric|max:9999999999.99',
            'truila_url'       => 'nullable|',
            'truila_est'       => 'nullable|numeric|max:9999999999.99',
            'har_url'          => 'nullable|',
            'har_est'          => 'nullable|numeric|max:9999999999.99',
            'beenverified_url' => 'nullable|',
        ];

    }

    public static function priceHistoryCreateValidation()
    {
        return $rules = [

            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'price_date'    => 'nullable|date|date_format:"Y-m-d',
            'price'         => 'nullable|numeric|max:9999999999.99',
            'cost_per_sqft' => 'nullable|numeric|max:9999999999.99',
            'source'        => 'nullable|',
            'description'   => [
                Rule::in(array_keys(config('property_information.ph_description'))),
            ],

        ];

    }

    public static function priceHistoryUpdateValidation()
    {
        return $rules = [

            'price_date'    => 'nullable|date|date_format:"Y-m-d',
            'price'         => 'nullable|numeric|max:9999999999.99',
            'cost_per_sqft' => 'nullable|numeric|max:9999999999.99',
            'source'        => 'nullable|',
            'description'   => [
                Rule::in(array_keys(config('property_information.ph_description'))),
            ],

        ];

    }

    public static function schoolNeighborhoodValidation()
    {
        return $rules = [
            'house_id'          => 'required|numeric|exists:home_information,house_id',
            'elementary_school' => 'nullable|',
            'middle_school'     => 'nullable',
            'high_school'       => 'nullable|',

        ];

    }

}
