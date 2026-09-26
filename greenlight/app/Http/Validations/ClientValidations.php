<?php
/**
 * Created By Mranalinee Chouhan
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/1/18 8:15 PM
 */

namespace App\Http\Validations;

use Validator;
use Illuminate\Validation\Rule;

Use Log;

class ClientValidations
{

    public static function documentValidation()
    {

        return $rules = [
            'house_id'              => 'required|numeric|exists:home_information,house_id',
            'document_type'         => [ "required"
               // Rule::in(array_keys(config('property_information.non_hud_expenditures_sub_cat'))),
            ],

            'amount'                => 'nullable|numeric|max:9999999999.99',
            'document_date'         => 'nullable|date_format:"Y-m-d',
            'document'              => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:50360', # 50 MB approx
        ];
    }


    public static function masterClosingDocValidation()
    {

        return $rules = [
            'house_id'              => 'required|numeric|exists:home_information,house_id',
            'document_type'         => [
                "required",
                Rule::in(array_keys(config('property_information.master_closing_doc_type'))),
            ],

            'other_name'            => 'nullable',
            'case_number'           => 'nullable|numeric',
            'master_closing_date'   => 'nullable|date_format:"Y-m-d',
            'document'              => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }

    public static function clientMasterValidation()
    {

        return $rules = [

            'AA_name'                               => 'nullable',
            'referrer_name'                         => 'nullable',
            'lender_wire_route_account'             => 'nullable|numeric|max:9999999999.99',
            'lender_llc_interest'                   => 'nullable|numeric|max:9999999999.99',
            'lender_gross_interest'                 => 'nullable|numeric|max:9999999999.99',
            'after_30_days'                         => 'nullable|numeric|max:9999999999.99',
            'bank_charges'                          => 'nullable|numeric|max:9999999999.99',
            'open_field_1'                          => 'nullable',
            'dump_fee'                              => 'nullable|numeric|max:9999999999.99',
            'insurance'                             => 'nullable|numeric|max:9999999999.99',
            'utilities_internet_cameras_arlo_pro'   => 'nullable',
            'legal_fees'                            => 'nullable|numeric|max:9999999999.99',
            'llc_fees'                              => 'nullable|numeric|max:9999999999.99',
            'interest_expense'                      => 'nullable|numeric|max:9999999999.99',
            'renovation'                            => 'nullable|numeric|max:9999999999.99',
            'landscaping'                           => 'nullable|numeric|max:9999999999.99',
            'miscellaneous'                         => 'nullable|numeric|max:9999999999.99',
            'total_cost_a_b'                        => 'nullable|numeric|max:9999999999.99',
            'total_cost_b_c'                        => 'nullable|numeric|max:9999999999.99',
            'net_profit'                            => 'nullable|numeric|max:9999999999.99',
            'wired_to_trust'                        => 'nullable',
            'member1'                               => 'nullable',
            'member2'                               => 'nullable',
            'member3'                               => 'nullable',
        ];
    }



    public static function clientRenovationValidation()
    {

        return $rules = [
            'house_id'              => 'required|numeric|exists:home_information,house_id',
            'section_type'         => [
                "required",
                Rule::in(['invoices','expenses']),
            ],
            'invoice_date'          => 'required|date|date_format:"Y-m-d',
            'amount'                => 'nullable|numeric|max:9999999999.99',
            'paid_date'             => 'nullable',
            'paid'                  => 'nullable',
            'bank_deposite'         => 'nullable|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
            'bank_statement'        => 'nullable|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx

        ];
    }


    public static function clientRenovationUpdateValidation()
    {

        return $rules = [
            #'house_id'             => 'nullable|numeric|exists:home_information,house_id',
            'id'          => 'numeric|exists:client_renovation,id',
            'section_type'         => [
                "required",
                Rule::in(['invoices','expenses']),
            ],
            'invoice_date'          => 'required|date|date_format:"Y-m-d',
            'amount'                => 'nullable|numeric|max:9999999999.99',
            'paid_date'             => 'nullable',
            'paid'                  => 'nullable',
            'bank_deposite'         => 'nullable|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
            'bank_statement'        => 'nullable|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx

        ];
    }



    public static function nonHudExpenditureValidation()
    {

        return $rules = [
            'house_id'              => 'required|numeric|exists:home_information,house_id',
            'category'              => 'nullable',
            'sub_category'          => 'nullable',
            'sub_category_lable'    => 'nullable',
            'description'           => 'nullable',
            'amount'                => 'nullable|numeric|max:9999999999.99',
            'expenditure_date'      => 'nullable|date_format:"Y-m-d',
            'document'              => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
            'type'                  => 'nullable',

        ];
    }

    public static function nonHudExpenditureUpdateValidation()
    {

        return $rules = [
            'house_id'              => 'required|numeric|exists:home_information,house_id',
            'category'              => 'nullable',
            'sub_category'          => 'nullable',
            'sub_category_lable'    => 'nullable',
            'description'           => 'nullable',
            'amount'                => 'nullable|numeric|max:9999999999.99',
            'expenditure_date'      => 'nullable|date_format:"Y-m-d',
            'document'              => 'nullable|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
            'type'                  => 'nullable',

        ];
    }


    public static function clientInformationValidation()
    {

        return $rules = [
            'house_id'              => 'required|numeric|exists:home_information,house_id',
            'email_address'         => 'nullable',
            'phone_number'          => 'nullable|numeric',
            'bidding_llc'           => 'nullable',
            'asset_protected_llc'   => 'nullable',
            'effective_date'        => 'nullable|date_format:"Y-m-d',
            'description'           => 'nullable'

        ];
    }


    public static function payoutValidation()
    {

        return $rules = [
            'house_id'                => 'required|numeric|exists:home_information,house_id',
            'category_id'             => 'nullable',
            'amount'                  => 'nullable|numeric|max:9999999999.99',
        ];
    }

    public static function payoutCategoryValidation()
    {

        return $rules = [
            'id'             => 'required|numeric',
            'category_name'           => 'required',
        ];
    }

    public static function clientRenovationBudgetValidation()
    {

        return $rules = [
            'house_id'                                  => 'required|numeric|exists:home_information,house_id',

            'client_budget_for_renovation'              => 'nullable|numeric|max:9999999999.99',
            'added_budget_for_renovation'               => 'nullable|numeric|max:9999999999.99',
            'estimated_days_to_finish'                  => 'nullable|numeric',
            'estimated_budget_for_full_renovation'      => 'nullable|numeric|max:9999999999.99',
            'client_days_renovation'                    => 'nullable|numeric',
            'lender_funds_for_renovation'               => 'nullable|numeric|max:9999999999.99',
            'actual_days_until_finish'                  => 'nullable|numeric',
            'actual_budget_for_full_renovation'         => 'nullable|numeric|max:9999999999.99',
            'renovation_finish'                         =>  'nullable|date_format:"Y-m-d',

        ];
    }











}
