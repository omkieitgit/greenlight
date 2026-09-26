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

class WholeSaleBuyerValidations
{

    public static function strategyValidation()
    {
        return $rules = [
            'house_id'                      => 'required|numeric|exists:home_information,house_id',
            'is_manual_close_date_a_to_b'   => 'boolean',
            'est_close_date_a_to_b'         => 'nullable|date|date_format:"Y-m-d',
            'act_close_date_a_to_b'         => 'nullable|date|date_format:"Y-m-d',
            'calc_close_date_a_to_b'        => 'nullable|date|date_format:"Y-m-d',
            'is_manual_close_date_b_to_c'   => 'boolean',
            'est_close_date_b_to_c'         => 'nullable|date|date_format:"Y-m-d',
            'act_close_date_b_to_c'         => 'nullable|date|date_format:"Y-m-d',
            'calc_close_date_b_to_c'        => 'nullable|date|date_format:"Y-m-d',
            'est_days_start_to_finish'      => 'nullable|numeric|max:9999999999',
            'act_days_start_to_finish'      => 'nullable|numeric|max:9999999999',
            'calc_days_start_to_finish'     => 'nullable|numeric|max:9999999999',
            'est_days_on_market'            => 'nullable|numeric|max:9999999999',
            'act_days_on_market'            => 'nullable|numeric|max:9999999999',
            'calc_days_on_market'           => 'nullable|numeric|max:9999999999',
            'est_prp_rate_of_return'        => 'nullable|numeric|max:9999999999.99',
            'act_prp_rate_of_return'        => 'nullable|numeric|max:9999999999.99',
            'calc_prp_rate_of_return'       => 'nullable|numeric|max:9999999999.99',
            'est_ann_return_aft_fnl_close'  => 'nullable|numeric|max:9999999999.99',
            'act_ann_return_aft_fnl_close'  => 'nullable|numeric|max:9999999999.99',
            'calc_ann_return_aft_fnl_close' => 'nullable|numeric|max:9999999999.99',
            'est_total_cost_to_buy_a_to_b'  => 'nullable|numeric|max:9999999999.99',
            'act_total_cost_to_buy_a_to_b'  => 'nullable|numeric|max:9999999999.99',
            'calc_total_cost_to_buy_a_to_b' => 'nullable|numeric|max:9999999999.99',
            'est_total_cost_to_buy_b_to_c'  => 'nullable|numeric|max:9999999999.99',
            'act_total_cost_to_buy_b_to_c'  => 'nullable|numeric|max:9999999999.99',
            'calc_total_cost_to_buy_b_to_c' => 'nullable|numeric|max:9999999999.99',
            'est_net_profit'                => 'nullable|numeric|max:9999999999.99',
            'act_net_profit'                => 'nullable|numeric|max:9999999999.99',
            'calc_net_profit'               => 'nullable|numeric|max:9999999999.99',
            'net_payout_per'                => 'nullable|numeric|max:9999999999.99',
            'est_payout_split'              => 'nullable|numeric|max:9999999999.99',
            'act_payout_split'              => 'nullable|numeric|max:9999999999.99',
            'calc_payout_split'             => 'nullable|numeric|max:9999999999.99',
            'net_payout_founder'            => 'nullable|numeric|max:9999999999.99',
            'est_net_payout'                => 'nullable|numeric|max:9999999999.99',
            'act_net_payout'                => 'nullable|numeric|max:9999999999.99',
            'calc_net_payout'               => 'nullable|numeric|max:9999999999.99',
            'date_listed'                   => 'nullable|date|date_format:"Y-m-d',
            'date_under_contract'           => 'nullable|date|date_format:"Y-m-d',
            'date_sold'                     => 'nullable|date|date_format:"Y-m-d',
            'actual_days_on_market'         => 'nullable|numeric|max:9999999999',
            'lf_dead_property'              => 'boolean',
            'lf_wo_auction_outbid'          => 'boolean',
            'upst_auction_no_bid'           => 'boolean',
            'potential_buy'                 => 'boolean',
            'property_in_escrow'            => 'boolean',
            'list_and_flip'                 => 'boolean',
            'bid_offer_on_property'         => 'boolean',
            'property_closed'               => 'boolean',
            'property_closed_date'          => 'nullable|date|date_format:"Y-m-d',
            'total_days_to_sell'            => 'nullable|numeric|max:9999999999',
            'purchased_deed'                => 'boolean',
            'bidding_in_process'            => 'boolean',
            'assignment'                    => 'boolean',
            'bid_offer_confirmed'           => 'boolean',
            'deposit_to_be_returned'        => 'boolean',
            'exclusive_agency'              => 'boolean',
            'off_site_or_no_sale'           => 'boolean',
            'property_purchased_acq_a_to_b' => 'boolean',
            'dead_property'                 => 'boolean',
            'attended_sale_outbid'          => 'boolean',
            'attended_sale_no_bid'          => 'boolean',
            'property_not_purchased'        => 'boolean',

        ];
    }


    public static function emailsAmCreateValidation()
    {
        return $rules = [

            'house_id' => 'required|numeric|exists:home_information,house_id',
            'user_id'  => 'nullable|numeric|exists:users,id',
            'email'    => 'nullable|email',
        ];

    }

    public static function emailsAmUpdateValidation()
    {
        return $rules = [
            'user_id' => 'nullable|numeric|exists:users,id',
            'email'   => 'nullable|email',
        ];

    }

    public static function emailsCompanyTeamMemberCreateValidation()
    {
        return $rules = [

            'house_id' => 'required|numeric|exists:home_information,house_id',
            'user_id'  => 'nullable|numeric|exists:users,id',
            'email'    => 'nullable|email',
            'username' => 'nullable',
        ];

    }

    public static function emailsCompanyTeamMemberUpdateValidation()
    {
        return $rules = [
            'user_id'  => 'nullable|numeric|exists:users,id',
            'email'    => 'nullable|email',
            'username' => 'nullable',
        ];

    }

    public static function emailsFunderLenderCreateValidation()
    {
        return $rules = [

            'house_id' => 'required|numeric|exists:home_information,house_id',
            'user_id'  => 'nullable|numeric|exists:users,id',
            'email'    => 'nullable|email',
        ];

    }

    public static function emailsFunderLenderUpdateValidation()
    {
        return $rules = [
            'user_id' => 'nullable|numeric|exists:users,id',
            'email'   => 'nullable|email',
        ];

    }

    public static function emailsTimeLeftNoticeCreateValidation()
    {
        return $rules = [

            'house_id' => 'required|numeric|exists:home_information,house_id',
            'user_id'  => 'nullable|numeric|exists:users,id',
            'email'    => 'nullable|email',
            'is_check' => 'boolean',
        ];

    }

    public static function emailsTimeLeftNoticeUpdateValidation()
    {
        return $rules = [
            'user_id'  => 'nullable|numeric|exists:users,id',
            'email'    => 'nullable|email',
            'is_check' => 'boolean',
        ];

    }


    public static function wholesaleBuyerNCreateValidation()
    {
        return $rules = [

            'house_id'          => 'required|numeric|exists:home_information,house_id',
            'user_id'           => 'nullable|numeric|exists:users,id',
            'email'             => 'nullable|email',
            'name'              => 'nullable',
            'receive_update'    => [
                'nullable',
                Rule::in([1,2,3,4,5,6,0]), ## 0 for none
            ],
            'est_amount'        => 'nullable|numeric|max:9999999999',
            'est_percent'       => 'nullable|numeric|max:9999999999',
            'est_profit'        => 'nullable|numeric|max:9999999999',
            'est_payoff_amount' => 'nullable|numeric|max:9999999999',
            'act_amount'        => 'nullable|numeric|max:9999999999',
            'act_percent'       => 'nullable|numeric|max:9999999999',
            'act_profit'        => 'nullable|numeric|max:9999999999',
            'act_payoff_amount' => 'nullable|numeric|max:9999999999',
            'added_by'          => 'nullable|numeric|exists:users,id',

        ];

    }

    public static function wholesaleBuyerNUpdateValidation()
    {
        return $rules = [
            'user_id'           => 'nullable|numeric|exists:users,id',
            'email'             => 'nullable|email',
            'name'              => 'nullable',
            'receive_update'    => [
                'nullable',
                Rule::in([1,2,3,4,5,6,0]), ## 0 for none
            ],
            'est_amount'        => 'nullable|numeric|max:9999999999',
            'est_percent'       => 'nullable|numeric|max:9999999999',
            'est_profit'        => 'nullable|numeric|max:9999999999',
            'est_payoff_amount' => 'nullable|numeric|max:9999999999',
            'act_amount'        => 'nullable|numeric|max:9999999999',
            'act_percent'       => 'nullable|numeric|max:9999999999',
            'act_profit'        => 'nullable|numeric|max:9999999999',
            'act_payoff_amount' => 'nullable|numeric|max:9999999999',
            'added_by'          => 'nullable|numeric|exists:users,id',
        ];

    }

    public static function wholesaleBuyerNTotalUpdateValidation()
    {
        return $rules = [
            'house_id'          => 'required|numeric|exists:home_information,house_id',
            'est_amount'        => 'nullable|numeric|max:9999999999',
            'est_percent'       => 'nullable|numeric|max:9999999999',
            'est_profit'        => 'nullable|numeric|max:9999999999',
            'est_payoff_amount' => 'nullable|numeric|max:9999999999',
            'act_amount'        => 'nullable|numeric|max:9999999999',
            'act_percent'       => 'nullable|numeric|max:9999999999',
            'act_profit'        => 'nullable|numeric|max:9999999999',
            'act_payoff_amount' => 'nullable|numeric|max:9999999999',
        ];

    }

}
