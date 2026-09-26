<?php
/**
 * Created By Rativardhan Singh Sengar  1/14/19 7:09 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/14/19 7:33 AM
 */

namespace App\Models;

use App\Services\SaleDetailsDescriptionsService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class MortgageLiensModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'mortgage_liens';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'mortgage_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    //protected $guarded = ['house_id'];
    protected $fillable = [
        'house_id', 'lien_type', 'lien_foreclosing', 'no_str_no_appt', 'defective_lien', 'lender', 'lien_amount'
        , 'date_recorded', 'dt_book_page', 'assignment_bp', 'loan_type', 'loan_term', 'maturity_date', 'right_to_cure'
        , 'trustee_fees', 'str_book_page', 'str_date', 'trustee', 'reasonable_attorney_fees', 'estimated_a_match', 'dt_nos'
        , 'est_late_payment_and_fees', 'est_equity', 'total_est_debt', 'amortization_calculation', 'amortization_annual_interest'
        , 'amortization_monthly_payment', 'amortization_monthly_principal_payment', 'amortization_monthly_interest_payment'
        , 'amortization_loan_estimate_balance', 'modification_agreement', 'modification_book_page', 'modification_date'
        , 'modification_lien_amount', 'modification_loan_term', 'modification_maturity_date', 'modification_annual_interest'
        , 'modification_monthly_payment', 'modification_est_late_payment_and_fees',
        'modification_loan_estimate_balance', 'subordination_agreement', 'sub_a_book_page', 'sub_a_date', 'sub_lien_position'
        , 'property_owner_1', 'property_owner_2', 'property_owner_3', 'property_owner_4', 'company_not_current'
        , 'dtc_first_check', 'dca_second_check', 'dca_final_check','es_excess_funds',
        'redemption_info','redemption_notice','redemption_date','red_by_owner','redemption_expires',
        'foreclosure_result','trdeep_instrument','trdeed_date','winning_bidder','winning_bid','instrument','total_debt','judgement_date','judgement_record'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    public function mortgage_liens_document()
    {
        return $this->hasMany(DocumentMortgage::class,"mortgage_id","mortgage_id");
    }

    public function check_by()
    {
        return $this->hasMany(MortgageLiensCheckbyModel::class,"mortgage_id","mortgage_id");
    }

    public function compSection(){
        return $this->hasMany(CompSectionHistoryModel::class,"foreign_id","mortgage_id")->whereIn("section_type",["1","2","3","4","5"]);
    }
}
