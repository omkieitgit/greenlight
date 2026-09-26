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

class SaleDetailsValidations
{

    public static function saleDetailsCreateValidation()
    {
        return $rules = [
            'house_id'               => 'required|numeric|exists:home_information,house_id',
            'sale_date'              => 'nullable|date|date_format:"Y-m-d',
            'case_number'            => 'nullable',
            'opening_bid'            => 'nullable',
            'sale_type'              => [
                'nullable',
                Rule::in(array_keys(config('property_information.sale_type'))),
            ],
            'sale_status'            => [
                'nullable',
                Rule::in(array_keys(config('property_information.sale_status'))),
            ],
            'sale_place'             => 'nullable|max:255',
            'sale_time'              => 'nullable',
            'trustee_file_no'        => 'nullable',
            'priceint'               => 'nullable|integer|max:20',
            'trustee_scraped'        => 'nullable',
            'trustee'                => 'nullable',
            'trustee_url'            => 'nullable|max:255',
            'trustee_address'        => 'nullable',
            'trustee_phone'          => 'nullable',
            'trustee_hours'          => 'nullable',
            'legal_notice_url'       => 'nullable|max:255',
            'legal_date_pulled'      => 'nullable|date|date_format:"Y-m-d',
            'auction_com_url'        => 'nullable|max:255',
            'auction_date_pulled'    => 'nullable|date|date_format:"Y-m-d',
            'newspapaer_url'         => 'nullable|max:255',
            'newspapaer_date_pulled' => 'nullable|date|date_format:"Y-m-d',
        ];
    }

    public static function saleDetailsUpdateValidation()
    {
        return $rules = [
            'sale_date'              => 'nullable|date|date_format:"Y-m-d',
            'case_number'            => 'nullable',
            'opening_bid'            => 'nullable|numeric|max:9999999999.99',
            'sale_type'              => [
                'nullable',
                Rule::in(array_keys(config('property_information.sale_type'))),
            ],
            'sale_status'            => [
                'nullable',
                Rule::in(array_keys(config('property_information.sale_status'))),
            ],
            'sale_place'             => 'nullable|max:255',
            'sale_time'              => 'nullable',
            'trustee_file_no'        => 'nullable',
            'priceint'               => 'nullable|integer|max:20',
            'trustee_scraped'        => 'nullable',
            'trustee'                => 'nullable',
            'trustee_url'            => 'nullable|max:255',
            'trustee_address'        => 'nullable',
            'trustee_phone'          => 'nullable',
            'trustee_hours'          => 'nullable',
            'legal_notice_url'       => 'nullable|max:255',
            'legal_date_pulled'      => 'nullable|date|date_format:"Y-m-d',
            'auction_com_url'        => 'nullable|max:255',
            'auction_date_pulled'    => 'nullable|date|date_format:"Y-m-d',
            'newspapaer_url'         => 'nullable|max:255',
            'newspapaer_date_pulled' => 'nullable|date|date_format:"Y-m-d',
        ];
    }


    public static function saleDetailsCreateBidderValidation()
    {

        return $rules = [
            'sale_id'                => 'required|numeric|exists:sale_details,sale_id',
            'name_upset_bidder'      => 'nullable',
            'amount_of_bid'          => 'nullable|numeric|max:9999999999.99',
            'bid_date'               => 'nullable|date|date_format:"Y-m-d',
            'last_date_to_upset_bid' => 'nullable|date|date_format:"Y-m-d',
            'min_amt_nxt_ub'         => 'nullable|numeric|max:9999999999.99',
            'email'                  => 'nullable',
            'address'                => 'nullable',
            'phone'                  => 'nullable|max:15',
            'date_of_sale_report'    => 'nullable|date|date_format:"Y-m-d',
            'deposit_upset'          => 'nullable|numeric|max:9999999999.99',
            'name_of_mortage'        => 'nullable',
            'name_of_cryer'          => 'nullable',
            'bid_confirmed'          => [
                "nullable",Rule::in([1, 0, true,false]),
            ],
            'bid_upset'              => [
                "nullable",Rule::in([1, 0, true,false]),
            ],
        ];
    }

    public static function saleDetailsUpdateBidderValidation()
    {
        return $rules = [
            #'bidder_id'                => 'required|numeric|exists:sale_details,bidder_id',
            'name_upset_bidder'      => 'nullable',
            'amount_of_bid'          => 'nullable|numeric|max:9999999999.99',
            'bid_date'               => 'nullable|date|date_format:"Y-m-d',
            'last_date_to_upset_bid' => 'nullable|date|date_format:"Y-m-d',
            'min_amt_nxt_ub'         => 'nullable|numeric|max:9999999999.99',
            'email'                  => 'nullable',
            'address'                => 'nullable',
            'phone'                  => 'nullable|max:15',
            'date_of_sale_report'    => 'nullable|date|date_format:"Y-m-d',
            'deposit_upset'          => 'nullable|numeric|max:9999999999.99',
            'name_of_mortage'        => 'nullable',
            'name_of_cryer'          => 'nullable',
            'bid_confirmed'          => [
                "nullable",Rule::in([1, 0, true,false]),

            ],
            'bid_upset'              => [
                "nullable",Rule::in([1, 0, true,false]),
            ],
        ];
    }



    public static function documentValidation()
    {
        return $rules = [
            'sale_id'       => 'required|numeric|exists:sale_details,sale_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.sale_doc_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'case_number'   => 'nullable',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:50000', # 15 MB approx
        ];
    }

    public static function documentBidderValidation()
    {
        return $rules = [
            'bidder_id'       => 'required|numeric|exists:sale_bidder,bidder_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'case_number'   => 'nullable',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }
}
