<?php
/**
 * Created By Mranalinee Chouhan
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified  
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class PropertyAcquisitionAtoBSecondModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'property_acquisition_a_to_b_second';

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
        'house_id', 'house_construction_est', 'house_construction_act', 'house_construction_diff', 'house_construction_calc', 'locks_est', 'locks_act', 'locks_diff', 'locks_calc', 'eviction_est', 'eviction_act', 'eviction_diff', 'eviction_calc', 'irs_tax_liens_est', 'irs_tax_liens_act', 'irs_tax_liens_diff', 'irs_tax_liens_calc', 'irs_tax_liens_date_est', 'irs_tax_liens_date_act', 'irs_tax_liens_date_diff', 'irs_tax_liens_date_calc', 'attorney_closing_est', 'attorney_closing_act', 'attorney_closing_diff', 'attorney_closing_calc', 'lawn_care_est', 'lawn_care_act', 'lawn_care_diff', 'lawn_care_calc', 'home_inspection_est', 'home_inspection_act', 'home_inspection_diff', 'home_inspection_calc', 'inspection_fee_est', 'inspection_fee_act', 'inspection_fee_diff', 'inspection_fee_calc', 'title_first_one', 'title_first_two', 'title_first_three', 'title_first_four', 'title_first_fifth', 'title_second_one', 'title_second_two', 'title_second_three', 'title_second_four', 'title_second_fifth', 'legal_est', 'legal_act', 'legal_diff', 'legal_calc', 'total_additional_cost_est', 'total_additional_cost_act', 'total_additional_cost_diff', 'total_additional_cost_calc', 'total_cost_to_buy_a_to_b_est', 'total_cost_to_buy_a_to_b_act', 'total_cost_to_buy_a_to_b_diff', 'total_cost_to_buy_a_to_b_calc'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


}         


