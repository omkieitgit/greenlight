<?php
/**
 * Created By Rativardhan Singh Sengar  1/13/19 11:58 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/26/18 1:00 PM
 */

namespace App\Http\Validations;

use Validator;
use Illuminate\Validation\Rule;

Use Log;

class MortgageOtherLiensPropertyTaxesValidations
{
    private $request;


    public static function mortgageOtherLiensPropertyTaxesUpdateValidation()
    {
        return $rules = [
            'house_id'                => 'required|numeric|exists:home_information,house_id',
            'county_rod_ur'           => 'nullable|max:255',
            'manual_search'           => 'nullable',
            'no_active_mortgage_lien' => 'boolean',
        ];
    }

    public static function liensCreateValidation($house_id)
    {
        return $rules = [
            'house_id'                               => 'required|numeric|exists:home_information,house_id',
            'lien_type'                              => [
                "required",
                Rule::in([1, 2, 3,4]),
                'unique:mortgage_liens,lien_type,NULL,NULL,house_id,' . $house_id
            ],
            'lien_foreclosing'                       => 'boolean',
            'no_str_no_appt'                         => 'boolean',
            'defective_lien'                         => 'boolean',
            'lender'                                 => 'nullable',
            'lien_amount'                            => 'nullable|numeric|max:9999999999.99',
            'date_recorded'                          => 'nullable|date|date_format:"Y-m-d',
            'dt_book_page'                           => 'nullable',
            'assignment_bp'                          => 'nullable',
            'loan_type'                              => [
                "nullable",
                Rule::in(array_keys(config('property_information.loan_type'))),
            ],
            'loan_term'                              => 'nullable',
            'maturity_date'                          => 'nullable|date|date_format:"Y-m-d',
            'right_to_cure'                          => 'nullable',
            'trustee_fees'                           => 'nullable|numeric|max:9999999999.99',
            'str_book_page'                          => 'nullable',
            'str_date'                               => 'nullable|date|date_format:"Y-m-d',
            'trustee'                                => 'nullable',
            'reasonable_attorney_fees'               => 'boolean',
            'estimated_a_match'                      => 'boolean',
            'est_equity'                             => 'nullable|numeric|max:9999999999.99',
            'total_est_debt'                         => 'nullable|numeric|max:9999999999.99',
            'amortization_calculation'               => 'boolean',
            'dt_nos'                                 => 'boolean',
            'est_late_payment_and_fees'              => 'nullable|numeric|max:9999999999.99',
            'amortization_annual_interest'           => 'nullable|numeric|max:9999999999.99',
            'amortization_monthly_payment'           => 'nullable|numeric|max:9999999999.99',
            'amortization_monthly_principal_payment' => 'nullable|numeric|max:9999999999.99',
            'amortization_monthly_interest_payment'  => 'nullable|numeric|max:9999999999.99',
            'amortization_loan_estimate_balance'     => 'nullable|numeric|max:9999999999.99',
            'modification_agreement'                 => 'boolean',
            'modification_book_page'                 => 'nullable',
            'modification_date'                      => 'nullable|date|date_format:"Y-m-d',
            'modification_lien_amount'               => 'nullable|numeric|max:9999999999.99',
            'modification_loan_term'                 => 'nullable',
            'modification_maturity_date'             => 'nullable|date|date_format:"Y-m-d',
            'modification_annual_interest'           => 'nullable|numeric|max:9999999999.99',
            'modification_monthly_payment'           => 'nullable|numeric|max:9999999999.99',
            'modification_est_late_payment_and_fees' => 'nullable|numeric|max:9999999999.99',
            'modification_loan_estimate_balance'     => 'nullable|numeric|max:9999999999.99',

            'subordination_agreement'                => 'boolean',
            'sub_a_book_page'                        => 'nullable',
            'sub_a_date'                             => 'nullable|date|date_format:"Y-m-d',
            'sub_lien_position'                      => 'nullable',
            'property_owner_1'                       => 'boolean',
            'property_owner_2'                       => 'boolean',
            'property_owner_3'                       => 'boolean',
            'property_owner_4'                       => 'boolean',
            'company_not_current'                    => 'nullable',

        ];

    }

    public static function liensUpdateValidation()
    {
        return $rules = [
            //'house_id'                               => 'required|numeric|exists:home_information,house_id',
            'lien_foreclosing'                       => 'boolean',
            'no_str_no_appt'                         => 'boolean',
            'defective_lien'                         => 'boolean',
            'lender'                                 => 'nullable',
            'lien_amount'                            => 'nullable|numeric|max:9999999999.99',
            'date_recorded'                          => 'nullable|date|date_format:"Y-m-d',
            'dt_book_page'                           => 'nullable',
            'assignment_bp'                          => 'nullable',
            'loan_type'                              => [
                "nullable",
                Rule::in(array_keys(config('property_information.loan_type'))),
            ],
            'loan_term'                              => 'nullable',
            'maturity_date'                          => 'nullable|date|date_format:"Y-m-d',
            'right_to_cure'                          => 'nullable',
            'trustee_fees'                           => 'nullable|numeric|max:9999999999.99',
            'str_book_page'                          => 'nullable',
            'str_date'                               => 'nullable|date|date_format:"Y-m-d',
            'trustee'                                => 'nullable',
            'reasonable_attorney_fees'               => 'boolean',
            'estimated_a_match'                      => 'boolean',
            'est_equity'                             => 'nullable|numeric|max:9999999999.99',
            'total_est_debt'                         => 'nullable|numeric|max:9999999999.99',
            'amortization_calculation'               => 'boolean',
            'dt_nos'                                 => 'boolean',
            'est_late_payment_and_fees'              => 'nullable|numeric|max:9999999999.99',
            'amortization_annual_interest'           => 'nullable|numeric|max:9999999999.99',
            'amortization_monthly_payment'           => 'nullable|numeric|max:9999999999.99',
            'amortization_monthly_principal_payment' => 'nullable|numeric|max:9999999999.99',
            'amortization_monthly_interest_payment'  => 'nullable|numeric|max:9999999999.99',
            'amortization_loan_estimate_balance'     => 'nullable|numeric|max:9999999999.99',
            'modification_agreement'                 => 'boolean',
            'modification_book_page'                 => 'nullable',
            'modification_date'                      => 'nullable|date|date_format:"Y-m-d',
            'modification_lien_amount'               => 'nullable|numeric|max:9999999999.99',
            'modification_loan_term'                 => 'nullable',
            'modification_maturity_date'             => 'nullable|date|date_format:"Y-m-d',
            'modification_annual_interest'           => 'nullable|numeric|max:9999999999.99',
            'modification_monthly_payment'           => 'nullable|numeric|max:9999999999.99',
            'modification_est_late_payment_and_fees' => 'nullable|numeric|max:9999999999.99',
            'modification_loan_estimate_balance'     => 'nullable|numeric|max:9999999999.99',
            'subordination_agreement'                => 'boolean',
            'sub_a_book_page'                        => 'nullable',
            'sub_a_date'                             => 'nullable|date|date_format:"Y-m-d',
            'sub_lien_position'                      => 'nullable',
            'property_owner_1'                       => 'boolean',
            'property_owner_2'                       => 'boolean',
            'property_owner_3'                       => 'boolean',
            'property_owner_4'                       => 'boolean',
            'company_not_current'                    => 'nullable',

        ];

    }

    public static function liensDocumentValidation()
    {
        return $rules = [
            'mortgage_id'   => 'required|numeric|exists:mortgage_liens,mortgage_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.mortgage_document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'case_number'   => 'nullable',
            'store_name'    => 'nullable',
            'book_page'     => 'nullable',
            'lien_amount'   => 'nullable|numeric|max:9999999999.99',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }

    public static function mortgageOtherUpdateValidation()
    {
        return $rules = [
            'house_id'                => 'required|numeric|exists:home_information,house_id',
            'lender'                  => 'nullable',
            'lien_amount'             => 'nullable|numeric|max:9999999999.99',
            'date_recorded'           => 'nullable|date|date_format:"Y-m-d',
            'book_page_assignment_bp' => 'nullable',
            'assignment_bp'           => 'nullable',
        ];

    }

    public static function mortgageTaxUpdateValidation()
    {
        return $rules = [
            'house_id'                => 'required|numeric|exists:home_information,house_id',
            'tax_name'                  => 'nullable',
            'tax_lien_amount'             => 'nullable|numeric|max:9999999999.99',
            'date_of_tax_lien'           => 'nullable|date|date_format:"Y-m-d',
        ];

    }

    public static function otherDocumentValidation()
    {
        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.mortgage_document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'book_page'     => 'nullable',
            'lien_amount'   => 'nullable|numeric|max:9999999999.99',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }

    public static function mortgageHoaUpdateValidation()
    {
        return $rules = [
            'house_id'             => 'required|numeric|exists:home_information,house_id',
            'defective_notice_hoa' => 'boolean',
            'hoa_lien_foreclosing' => 'boolean',
            'manual_search_hoa'    => 'boolean',
            'no_str'               => 'boolean',
            'hoa_name'             => 'nullable',
            'hoa_lien_amount'      => 'nullable|numeric|max:9999999999.99',
            'date_of_hoa_lien'     => 'nullable|date|date_format:"Y-m-d',
            'hoa_lien_book_page'   => 'nullable',
            'str_date'             => 'nullable|date|date_format:"Y-m-d',
            'str_book_page'        => 'nullable',
            'trustee_hoa'          => 'nullable',
            'prop_sign_owner_1'    => 'boolean',
            'prop_sign_owner_2'    => 'boolean',
            'prop_sign_owner_3'    => 'boolean',
            'prop_sign_owner_4'    => 'boolean',
            'company_not_ct_rcd'   => 'boolean',
            'dtc_first_check'      => 'boolean',
            'dca_second_check'     => 'boolean',
            'dca_final_check'      => 'boolean',
        ];

    }

    public static function hoaDocumentValidation()
    {
        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.mortgage_document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'book_page'     => 'nullable',
            'lien_amount'   => 'nullable|numeric|max:9999999999.99',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }

    public static function mortgagePropertyTaxesUpdateValidation()
    {
        return $rules = [
            'house_id'                  => 'required|numeric|exists:home_information,house_id',
            'total_property_taxes_owed' => 'nullable|numeric|max:9999999999.99',
            'treasure_url'              => 'nullable',
            'tax_bill_url'              => 'nullable',
        ];

    }

    public static function propertyTaxesDocumentValidation()
    {
        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.mortgage_document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }

    public static function createTaxesOwedValidation()
    {
        return $rules = [

            'house_id'   => 'required|numeric|exists:home_information,house_id',
            'taxes_year' => 'nullable|date_format:"Y',
            'taxes_owed' => 'nullable|numeric|max:9999999999.99',
        ];

    }

    public static function updateTaxesOwedValidation()
    {
        return $rules = [
            'taxes_year' => 'nullable|date_format:"Y',
            'taxes_owed' => 'nullable|numeric|max:9999999999.99',

        ];

    }

    public static function notesCreateValidation()
    {
        return $rules = [
            'house_id'                => 'required|numeric|exists:home_information,house_id',
            'lien_type'                              => [
                "required",
                Rule::in([1, 2, 3, 4,5,6]),
            ],
            'notes'           => 'required',
        ];
    }

    public static function taxDocumentValidation()
    {
        return $rules = [
            'house_id'      => 'required|numeric|exists:home_information,house_id',
            'document_type' => [
                "required",
                Rule::in(array_keys(config('property_information.mortgage_document_type'))),
            ],
            'other_name'    => "required_if:document_type,99",
            'org_name'      => 'nullable',
            'store_name'    => 'nullable',
            'book_page'     => 'nullable',
            'lien_amount'   => 'nullable|numeric|max:9999999999.99',
            'document_date' => 'nullable|date|date_format:"Y-m-d',
            'document'      => 'required|mimes:pdf,doc,docx,txt,jpeg,png,jpg,gif|max:15360', # 15 MB approx
        ];
    }

}
