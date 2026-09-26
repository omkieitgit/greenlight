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

class PropertyAcquisitionAtoBValidations
{

    public static function updateValidation()
    {
        return $rules = [

            'house_id' => 'required|numeric|exists:home_information,house_id',

            ## First
            'is_manual_contract_purchase_price_est'     => 'boolean',
            'contract_purchase_price_est'               => 'nullable|numeric|max:9999999999.99',
            'is_manual_contract_purchase_price_act'     => 'boolean',
            'contract_purchase_price_act'               => 'nullable|numeric|max:9999999999.99',
            'contract_purchase_price_diff'              => 'nullable|numeric|max:9999999999.99',
            'contract_purchase_price_calc'              => 'nullable|numeric|max:9999999999.99',
            'attorney_fees_litigation_est'              => 'nullable|numeric|max:9999999999.99',
            'attorney_fees_litigation_act'              => 'nullable|numeric|max:9999999999.99',
            'attorney_fees_litigation_diff'             => 'nullable|numeric|max:9999999999.99',
            'attorney_fees_litigation_calc'             => 'nullable|numeric|max:9999999999.99',
            'hud_fees_buyer_est'                        => 'nullable|numeric|max:9999999999.99',
            'hud_fees_buyer_act'                        => 'nullable|numeric|max:9999999999.99',
            'hud_fees_buyer_diff'                       => 'nullable|numeric|max:9999999999.99',
            'hud_fees_buyer_calc'                       => 'nullable|numeric|max:9999999999.99',
            'lenders_title_insurance_est'               => 'nullable|numeric|max:9999999999.99',
            'lenders_title_insurance_act'               => 'nullable|numeric|max:9999999999.99',
            'lenders_title_insurance_diff'              => 'nullable|numeric|max:9999999999.99',
            'lenders_title_insurance_calc'              => 'nullable|numeric|max:9999999999.99',
            'owner_title_insurance_est'                 => 'nullable|numeric|max:9999999999.99',
            'owner_title_insurance_act'                 => 'nullable|numeric|max:9999999999.99',
            'owner_title_insurance_diff'                => 'nullable|numeric|max:9999999999.99',
            'owner_title_insurance_calc'                => 'nullable|numeric|max:9999999999.99',
            'recording_est'                             => 'nullable|numeric|max:9999999999.99',
            'recording_act'                             => 'nullable|numeric|max:9999999999.99',
            'recording_diff'                            => 'nullable|numeric|max:9999999999.99',
            'recording_calc'                            => 'nullable|numeric|max:9999999999.99',
            'property_taxes_est'                        => 'nullable|numeric|max:9999999999.99',
            'property_taxes_act'                        => 'nullable|numeric|max:9999999999.99',
            'property_taxes_diff'                       => 'nullable|numeric|max:9999999999.99',
            'property_taxes_calc'                       => 'nullable|numeric|max:9999999999.99',
            'office_fee_est'                            => 'nullable|numeric|max:9999999999.99',
            'office_fee_act'                            => 'nullable|numeric|max:9999999999.99',
            'office_fee_diff'                           => 'nullable|numeric|max:9999999999.99',
            'office_fee_calc'                           => 'nullable|numeric|max:9999999999.99',
            'is_manual_loss_mitigation_on_deposits_est' => 'boolean',
            'loss_mitigation_on_deposits_est'           => 'nullable|numeric|max:9999999999.99',
            'loss_mitigation_on_deposits_act'           => 'nullable|numeric|max:9999999999.99',
            'loss_mitigation_on_deposits_diff'          => 'nullable|numeric|max:9999999999.99',
            'loss_mitigation_on_deposits_calc'          => 'nullable|numeric|max:9999999999.99',
            'sale_fee_est'                              => 'nullable|numeric|max:9999999999.99',
            'sale_fee_act'                              => 'nullable|numeric|max:9999999999.99',
            'sale_fee_diff'                             => 'nullable|numeric|max:9999999999.99',
            'sale_fee_calc'                             => 'nullable|numeric|max:9999999999.99',
            'llc_changes_est'                           => 'nullable|numeric|max:9999999999.99',
            'llc_changes_act'                           => 'nullable|numeric|max:9999999999.99',
            'llc_changes_diff'                          => 'nullable|numeric|max:9999999999.99',
            'llc_changes_calc'                          => 'nullable|numeric|max:9999999999.99',
            'utilities_est'                             => 'nullable|numeric|max:9999999999.99',
            'utilities_act'                             => 'nullable|numeric|max:9999999999.99',
            'utilities_diff'                            => 'nullable|numeric|max:9999999999.99',
            'utilities_calc'                            => 'nullable|numeric|max:9999999999.99',
            'is_manual_insurance_est'                   => 'boolean',
            'insurance_est'                             => 'nullable|numeric|max:9999999999.99',
            'insurance_act'                             => 'nullable|numeric|max:9999999999.99',
            'insurance_diff'                            => 'nullable|numeric|max:9999999999.99',
            'insurance_calc'                            => 'nullable|numeric|max:9999999999.99',
            'wire_fees_est'                             => 'nullable|numeric|max:9999999999.99',
            'wire_fees_act'                             => 'nullable|numeric|max:9999999999.99',
            'wire_fees_diff'                            => 'nullable|numeric|max:9999999999.99',
            'wire_fees_calc'                            => 'nullable|numeric|max:9999999999.99',
            'airport_transport_wire_est'                => 'nullable|numeric|max:9999999999.99',
            'airport_transport_wire_act'                => 'nullable|numeric|max:9999999999.99',
            'airport_transport_wire_diff'               => 'nullable|numeric|max:9999999999.99',
            'airport_transport_wire_calc'               => 'nullable|numeric|max:9999999999.99',
            'is_manual_excise_tax_nc_wire_est'          => 'boolean',
            'excise_tax_nc_wire_est'                    => 'nullable|numeric|max:9999999999.99',
            'excise_tax_nc_wire_act'                    => 'nullable|numeric|max:9999999999.99',
            'excise_tax_nc_wire_diff'                   => 'nullable|numeric|max:9999999999.99',
            'excise_tax_nc_wire_calc'                   => 'nullable|numeric|max:9999999999.99',

            ## Second
            'house_construction_est'        => 'nullable|numeric|max:9999999999.99',
            'house_construction_act'        => 'nullable|numeric|max:9999999999.99',
            'house_construction_diff'       => 'nullable|numeric|max:9999999999.99',
            'house_construction_calc'       => 'nullable|numeric|max:9999999999.99',
            'locks_est'                     => 'nullable|numeric|max:9999999999.99',
            'locks_act'                     => 'nullable|numeric|max:9999999999.99',
            'locks_diff'                    => 'nullable|numeric|max:9999999999.99',
            'locks_calc'                    => 'nullable|numeric|max:9999999999.99',
            'eviction_est'                  => 'nullable|numeric|max:9999999999.99',
            'eviction_act'                  => 'nullable|numeric|max:9999999999.99',
            'eviction_diff'                 => 'nullable|numeric|max:9999999999.99',
            'eviction_calc'                 => 'nullable|numeric|max:9999999999.99',
            'irs_tax_liens_est'             => 'nullable|numeric|max:9999999999.99',
            'irs_tax_liens_act'             => 'nullable|numeric|max:9999999999.99',
            'irs_tax_liens_diff'            => 'nullable|numeric|max:9999999999.99',
            'irs_tax_liens_calc'            => 'nullable|numeric|max:9999999999.99',
            'irs_tax_liens_date_est'        => 'nullable|date|date_format:"Y-m-d',
            'irs_tax_liens_date_act'        => 'nullable|date|date_format:"Y-m-d',
            'irs_tax_liens_date_diff'       => 'nullable|date|date_format:"Y-m-d',
            'irs_tax_liens_date_calc'       => 'nullable|date|date_format:"Y-m-d',
            'attorney_closing_est'          => 'nullable|numeric|max:9999999999.99',
            'attorney_closing_act'          => 'nullable|numeric|max:9999999999.99',
            'attorney_closing_diff'         => 'nullable|numeric|max:9999999999.99',
            'attorney_closing_calc'         => 'nullable|numeric|max:9999999999.99',
            'lawn_care_est'                 => 'nullable|numeric|max:9999999999.99',
            'lawn_care_act'                 => 'nullable|numeric|max:9999999999.99',
            'lawn_care_diff'                => 'nullable|numeric|max:9999999999.99',
            'lawn_care_calc'                => 'nullable|numeric|max:9999999999.99',
            'home_inspection_est'           => 'nullable|numeric|max:9999999999.99',
            'home_inspection_act'           => 'nullable|numeric|max:9999999999.99',
            'home_inspection_diff'          => 'nullable|numeric|max:9999999999.99',
            'home_inspection_calc'          => 'nullable|numeric|max:9999999999.99',
            'inspection_fee_est'            => 'nullable|numeric|max:9999999999.99',
            'inspection_fee_act'            => 'nullable|numeric|max:9999999999.99',
            'inspection_fee_diff'           => 'nullable|numeric|max:9999999999.99',
            'inspection_fee_calc'           => 'nullable|numeric|max:9999999999.99',
            'title_first_one'               => 'nullable',
            'title_first_two'               => 'nullable',
            'title_first_three'             => 'nullable',
            'title_first_four'              => 'nullable',
            'title_first_fifth'             => 'nullable',
            'title_second_one'              => 'nullable',
            'title_second_two'              => 'nullable',
            'title_second_three'            => 'nullable',
            'title_second_four'             => 'nullable',
            'title_second_fifth'            => 'nullable',
            'legal_est'                     => 'nullable|numeric|max:9999999999.99',
            'legal_act'                     => 'nullable|numeric|max:9999999999.99',
            'legal_diff'                    => 'nullable|numeric|max:9999999999.99',
            'legal_calc'                    => 'nullable|numeric|max:9999999999.99',
            'total_additional_cost_est'     => 'nullable|numeric|max:9999999999.99',
            'total_additional_cost_act'     => 'nullable|numeric|max:9999999999.99',
            'total_additional_cost_diff'    => 'nullable|numeric|max:9999999999.99',
            'total_additional_cost_calc'    => 'nullable|numeric|max:9999999999.99',
            'total_cost_to_buy_a_to_b_est'  => 'nullable|numeric|max:9999999999.99',
            'total_cost_to_buy_a_to_b_act'  => 'nullable|numeric|max:9999999999.99',
            'total_cost_to_buy_a_to_b_diff' => 'nullable|numeric|max:9999999999.99',
            'total_cost_to_buy_a_to_b_calc' => 'nullable|numeric|max:9999999999.99',

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

            

            