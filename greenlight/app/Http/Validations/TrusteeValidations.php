<?php
/**
 * Created By Rativardhan Singh Sengar  2/21/19 12:14 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/19/19 1:04 PM
 */

namespace App\Http\Validations;

use Validator;
use Illuminate\Validation\Rule;

Use Log;

class TrusteeValidations
{

    public static function updateOrCreateValidation()
    {

        return $rules = [
            'house_id'                      => 'required|numeric|exists:home_information,house_id',
            'trustee_deposit_returned'      => 'nullable|numeric|max:9999999999.99',
            'trustee_deposit_returned_date' => 'nullable|date|date_format:"Y-m-d',
            'trustee_returned_to'           => 'boolean',
            'county_deposit_returned'       => 'nullable|numeric|max:9999999999.99',
            'county_deposit_returned_date'  => 'nullable|date|date_format:"Y-m-d',
            'county_returned_to'            => 'boolean',
            'date_cost_wired_due_by'        => 'nullable|date|date_format:"Y-m-d',
            'amt_wired_close_due_by'        => 'nullable|numeric|max:9999999999.99',
            'est_upset_bid_percent'         => 'nullable|numeric|max:9999999999.99',
            'est_upset_bid_amount'          => 'nullable|numeric|max:9999999999.99',
            'est_upset_bid_manual_ip'       => 'boolean',
            'act_upset_bid_percent'         => 'nullable|numeric|max:9999999999.99',
            'act_upset_bid_amount'          => 'nullable|numeric|max:9999999999.99',
            'act_upset_bid_manual_ip'       => 'boolean',
            'diff_upset_bid_amount'         => 'nullable|numeric|max:9999999999.99',
        ];
    }
}
