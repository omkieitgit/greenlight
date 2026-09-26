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

class ClientBiddingFundsValidations
{

    public static function updateOrCreate()
    {
        return $rules = [
            'house_id' => 'required|numeric|exists:home_information,house_id',

            'bid_upset_date' => 'nullable|date|date_format:"Y-m-d',
            'last_date_for_next_upset' => 'nullable|date|date_format:"Y-m-d',
            'trustee_deposit_returned' => 'nullable',
            'trustee_deposit_returned_date' => 'nullable|date|date_format:"Y-m-d',
            'trustee_deposit_returned_to' => 'nullable',
            'county_deposit_returned' => 'nullable',
            'county_deposit_returned_date' => 'nullable|date|date_format:"Y-m-d',
            'county_deposit_returned_to' => 'nullable',
            'notes' => 'nullable',

        ];
    }


    public static function document()
    {
        return $rules = [
            'house_id' => 'required|numeric|exists:home_information,house_id',
            'bidding_type' => [
                "required",
                Rule::in(array_keys(config('property_information.bidding_type'))),
            ],
            'aa_account' => [
                "required",
                Rule::in(array_keys(config('property_information.aa_account'))),
            ],

            'org_name' => 'nullable',
            'store_name' => 'nullable',
            'amount' => 'nullable',
            'approval' => 'boolean',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document_authorization' => 'required_without:document_receipt|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
            'document_receipt' => 'required_without:document_authorization|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx

        ];
    }

}
