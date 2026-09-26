<?php
/**
 * Created By Rativardhan Singh Sengar  3/7/19 10:31 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/6/19 9:47 PM
 */

namespace App\Http\Validations;

use Validator;
use Illuminate\Validation\Rule;

Use Log;

class PropertyAcquisitionBtoCValidations
{

    public static function updateValidation()
    {
        return $rules = [

            'house_id'                        => 'required|numeric|exists:home_information,house_id',

            ## First
            'cma_arv_est'                     => 'nullable|numeric|max:9999999999.99',
            'cma_arv_act'                     => 'nullable|numeric|max:9999999999.99',
            'cma_arv_diff'                    => 'nullable|numeric|max:9999999999.99',
            'cma_arv_calc'                    => 'nullable|numeric|max:9999999999.99',
            'hud_fees_seller_est'             => 'nullable|numeric|max:9999999999.99',
            'hud_fees_seller_act'             => 'nullable|numeric|max:9999999999.99',
            'hud_fees_seller_diff'            => 'nullable|numeric|max:9999999999.99',
            'hud_fees_seller_calc'            => 'nullable|numeric|max:9999999999.99',
            'add_taxes_paid_est'              => 'nullable|numeric|max:9999999999.99',
            'add_taxes_paid_act'              => 'nullable|numeric|max:9999999999.99',
            'add_taxes_paid_diff'             => 'nullable|numeric|max:9999999999.99',
            'add_taxes_paid_calc'             => 'nullable|numeric|max:9999999999.99',
            'percent_less_title_service_est'  => 'nullable|numeric|max:9999999999.99',
            'less_title_service_est'          => 'nullable|numeric|max:9999999999.99',
            'percent_less_title_service_act'  => 'nullable|numeric|max:9999999999.99',
            'less_title_service_act'          => 'nullable|numeric|max:9999999999.99',
            'less_title_service_diff'         => 'nullable|numeric|max:9999999999.99',
            'percent_less_title_service_calc' => 'nullable|numeric|max:9999999999.99',
            'less_title_service_calc'         => 'nullable|numeric|max:9999999999.99',
            'percent_less_owner_policy_est'   => 'nullable|numeric|max:9999999999.99',
            'less_owner_policy_est'           => 'nullable|numeric|max:9999999999.99',
            'percent_less_owner_policy_act'   => 'nullable|numeric|max:9999999999.99',
            'less_owner_policy_act'           => 'nullable|numeric|max:9999999999.99',
            'less_owner_policy_diff'          => 'nullable|numeric|max:9999999999.99',
            'percent_less_owner_policy_calc'  => 'nullable|numeric|max:9999999999.99',
            'less_owner_policy_calc'          => 'nullable|numeric|max:9999999999.99',
            'seller_cooncession_est'          => 'nullable|numeric|max:9999999999.99',
            'seller_cooncession_act'          => 'nullable|numeric|max:9999999999.99',
            'seller_cooncession_diff'         => 'nullable|numeric|max:9999999999.99',
            'seller_cooncession_calc'         => 'nullable|numeric|max:9999999999.99',
            'title_service_cls_est'           => 'nullable|numeric|max:9999999999.99',
            'title_service_cls_act'           => 'nullable|numeric|max:9999999999.99',
            'title_service_cls_diff'          => 'nullable|numeric|max:9999999999.99',
            'title_service_cls_calc'          => 'nullable|numeric|max:9999999999.99',
            'ins_utl_misc_est'                => 'nullable|numeric|max:9999999999.99',
            'ins_utl_misc_act'                => 'nullable|numeric|max:9999999999.99',
            'ins_utl_misc_diff'               => 'nullable|numeric|max:9999999999.99',
            'ins_utl_misc_calc'               => 'nullable|numeric|max:9999999999.99',
            'percent_commission_est'          => 'nullable|numeric|max:9999999999.99',
            'commission_est'                  => 'nullable|numeric|max:9999999999.99',
            'percent_commission_act'          => 'nullable|numeric|max:9999999999.99',
            'commission_act'                  => 'nullable|numeric|max:9999999999.99',
            'commission_diff'                 => 'nullable|numeric|max:9999999999.99',
            'percent_commission_calc'         => 'nullable|numeric|max:9999999999.99',
            'commission_calc'                 => 'nullable|numeric|max:9999999999.99',
            'home_insurance_est'              => 'nullable|numeric|max:9999999999.99',
            'home_insurance_act'              => 'nullable|numeric|max:9999999999.99',
            'home_insurance_diff'             => 'nullable|numeric|max:9999999999.99',
            'home_insurance_calc'             => 'nullable|numeric|max:9999999999.99',

            # Second
            'irs_tax_liens_est'               => 'nullable|numeric|max:9999999999.99',
            'irs_tax_liens_act'               => 'nullable|numeric|max:9999999999.99',
            'irs_tax_liens_diff'              => 'nullable|numeric|max:9999999999.99',
            'irs_tax_liens_calc'              => 'nullable|numeric|max:9999999999.99',
            'web_fee_est'                     => 'nullable|numeric|max:9999999999.99',
            'web_fee_act'                     => 'nullable|numeric|max:9999999999.99',
            'web_fee_diff'                    => 'nullable|numeric|max:9999999999.99',
            'web_fee_calc'                    => 'nullable|numeric|max:9999999999.99',
            'data_input_est'                  => 'nullable|numeric|max:9999999999.99',
            'data_input_act'                  => 'nullable|numeric|max:9999999999.99',
            'data_input_diff'                 => 'nullable|numeric|max:9999999999.99',
            'data_input_calc'                 => 'nullable|numeric|max:9999999999.99',
            'accounting_services_est'         => 'nullable|numeric|max:9999999999.99',
            'accounting_services_act'         => 'nullable|numeric|max:9999999999.99',
            'accounting_services_diff'        => 'nullable|numeric|max:9999999999.99',
            'accounting_services_calc'        => 'nullable|numeric|max:9999999999.99',
            'tvl_exp_gas_est'                 => 'nullable|numeric|max:9999999999.99',
            'tvl_exp_gas_act'                 => 'nullable|numeric|max:9999999999.99',
            'tvl_exp_gas_diff'                => 'nullable|numeric|max:9999999999.99',
            'tvl_exp_gas_calc'                => 'nullable|numeric|max:9999999999.99',
            'lender_cost_points_est'          => 'nullable|numeric|max:9999999999.99',
            'lender_cost_points_act'          => 'nullable|numeric|max:9999999999.99',
            'lender_cost_points_diff'         => 'nullable|numeric|max:9999999999.99',
            'lender_cost_points_calc'         => 'nullable|numeric|max:9999999999.99',
            'lender_cost_interest_est'        => 'nullable|numeric|max:9999999999.99',
            'lender_cost_interest_act'        => 'nullable|numeric|max:9999999999.99',
            'lender_cost_interest_diff'       => 'nullable|numeric|max:9999999999.99',
            'lender_cost_interest_calc'       => 'nullable|numeric|max:9999999999.99',
            'title_first_one'                 => 'nullable',
            'title_first_two'                 => 'nullable',
            'title_first_three'               => 'nullable',
            'title_first_four'                => 'nullable',
            'title_first_fifth'               => 'nullable',
            'title_second_one'                => 'nullable',
            'title_second_two'                => 'nullable',
            'title_second_three'              => 'nullable',
            'title_second_four'               => 'nullable',
            'title_second_fifth'              => 'nullable',
            'legal_est'                       => 'nullable|numeric|max:9999999999.99',
            'legal_act'                       => 'nullable|numeric|max:9999999999.99',
            'legal_diff'                      => 'nullable|numeric|max:9999999999.99',
            'legal_calc'                      => 'nullable|numeric|max:9999999999.99',
            'is_manual_excise_tax'            => 'boolean',
            'excise_tax_est'                  => 'nullable|numeric|max:9999999999.99',
            'excise_tax_act'                  => 'nullable|numeric|max:9999999999.99',
            'excise_tax_diff'                 => 'nullable|numeric|max:9999999999.99',
            'excise_tax_calc'                 => 'nullable|numeric|max:9999999999.99',
            'county_tax_est'                  => 'nullable|numeric|max:9999999999.99',
            'county_tax_act'                  => 'nullable|numeric|max:9999999999.99',
            'county_tax_diff'                 => 'nullable|numeric|max:9999999999.99',
            'county_tax_calc'                 => 'nullable|numeric|max:9999999999.99',
            'costs_est'                       => 'nullable|numeric|max:9999999999.99',
            'costs_act'                       => 'nullable|numeric|max:9999999999.99',
            'costs_diff'                      => 'nullable|numeric|max:9999999999.99',
            'costs_calc'                      => 'nullable|numeric|max:9999999999.99',
            'total_est'                       => 'nullable|numeric|max:9999999999.99',
            'total_act'                       => 'nullable|numeric|max:9999999999.99',
            'total_diff'                      => 'nullable|numeric|max:9999999999.99',
            'total_calc'                      => 'nullable|numeric|max:9999999999.99',
            'net_spread_est'                  => 'nullable|numeric|max:9999999999.99',
            'net_spread_act'                  => 'nullable|numeric|max:9999999999.99',
            'net_spread_diff'                 => 'nullable|numeric|max:9999999999.99',
            'net_spread_calc'                 => 'nullable|numeric|max:9999999999.99',
            'net_profit_est'                  => 'nullable|numeric|max:9999999999.99',
            'net_profit_act'                  => 'nullable|numeric|max:9999999999.99',
            'net_profit_diff'                 => 'nullable|numeric|max:9999999999.99',
            'net_profit_calc'                 => 'nullable|numeric|max:9999999999.99',
            'total_cost_sell_b_to_c_est'      => 'nullable|numeric|max:9999999999.99',
            'total_cost_sell_b_to_c_act'      => 'nullable|numeric|max:9999999999.99',
            'total_cost_sell_b_to_c_diff'     => 'nullable|numeric|max:9999999999.99',
            'total_cost_sell_b_to_c_calc'     => 'nullable|numeric|max:9999999999.99',

            ## Darren
            'office_fee_check'   => 'boolean',
            'office_fee_date'    => 'nullable|date|date_format:"Y-m-d',
            'lmod_check'         => 'boolean',
            'lmod_date'          => 'nullable|date|date_format:"Y-m-d',
            'auction_check'      => 'boolean',
            'auction_date'       => 'nullable|date|date_format:"Y-m-d',
            'llc_check'          => 'boolean',
            'llc_date'           => 'nullable|date|date_format:"Y-m-d',
            'accounting_check'   => 'boolean',
            'accounting_date'    => 'nullable|date|date_format:"Y-m-d',
            'travel_check'       => 'boolean',
            'travel_date'        => 'nullable|date|date_format:"Y-m-d',
            'web_fee_check'      => 'boolean',
            'web_fee_check_date' => 'nullable|date|date_format:"Y-m-d',
            'data_input_check'   => 'boolean',
            'data_input_date'    => 'nullable|date|date_format:"Y-m-d',
            'inspection_check'   => 'boolean',
            'inspection_date'    => 'nullable|date|date_format:"Y-m-d',

        ];
    }

}

            

            