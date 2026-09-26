<?php
/**
 * Created By Rativardhan Singh Sengar  3/7/19 10:25 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/6/19 7:49 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class PropertyAcquisitionBtoCSecondModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'property_acquisition_b_to_c_second';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'house_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
   //protected $guarded = ['house_id'];
    protected $fillable = [
        'house_id', 'irs_tax_liens_est', 'irs_tax_liens_act', 'irs_tax_liens_diff', 'irs_tax_liens_calc', 'web_fee_est', 'web_fee_act',
        'web_fee_diff', 'web_fee_calc', 'data_input_est', 'data_input_act', 'data_input_diff', 'data_input_calc', 'accounting_services_est',
        'accounting_services_act', 'accounting_services_diff', 'accounting_services_calc', 'tvl_exp_gas_est', 'tvl_exp_gas_act', 'tvl_exp_gas_diff',
        'tvl_exp_gas_calc', 'lender_cost_points_est', 'lender_cost_points_act', 'lender_cost_points_diff', 'lender_cost_points_calc',
        'lender_cost_interest_est', 'lender_cost_interest_act', 'lender_cost_interest_diff', 'lender_cost_interest_calc', 'title_first_one',
        'title_first_two', 'title_first_three', 'title_first_four', 'title_first_fifth', 'title_second_one', 'title_second_two', 'title_second_three',
        'title_second_four', 'title_second_fifth', 'legal_est', 'legal_act', 'legal_diff', 'legal_calc', 'is_manual_excise_tax', 'excise_tax_est',
        'excise_tax_act', 'excise_tax_diff', 'excise_tax_calc', 'county_tax_est', 'county_tax_act', 'county_tax_diff', 'county_tax_calc', 'costs_est',
        'costs_act', 'costs_diff', 'costs_calc', 'total_est', 'total_act', 'total_diff', 'total_calc', 'net_spread_est', 'net_spread_act',
        'net_spread_diff', 'net_spread_calc', 'net_profit_est', 'net_profit_act', 'net_profit_diff', 'net_profit_calc', 'total_cost_sell_b_to_c_est',
        'total_cost_sell_b_to_c_act', 'total_cost_sell_b_to_c_diff', 'total_cost_sell_b_to_c_calc'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


}         


