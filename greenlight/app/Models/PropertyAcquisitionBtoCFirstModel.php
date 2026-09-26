<?php
/**
 * Created By Rativardhan Singh Sengar  3/7/19 10:24 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/6/19 7:48 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class PropertyAcquisitionBtoCFirstModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'property_acquisition_b_to_c_first';

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
        'house_id', 'cma_arv_est', 'cma_arv_act', 'cma_arv_diff', 'cma_arv_calc', 'hud_fees_seller_est',
        'hud_fees_seller_act', 'hud_fees_seller_diff', 'hud_fees_seller_calc', 'add_taxes_paid_est', 'add_taxes_paid_act',
        'add_taxes_paid_diff', 'add_taxes_paid_calc', 'percent_less_title_service_est', 'less_title_service_est',
        'percent_less_title_service_act', 'less_title_service_act', 'less_title_service_diff',
        'percent_less_title_service_calc', 'less_title_service_calc', 'percent_less_owner_policy_est',
        'less_owner_policy_est', 'percent_less_owner_policy_act', 'less_owner_policy_act', 'less_owner_policy_diff',
        'percent_less_owner_policy_calc', 'less_owner_policy_calc', 'seller_cooncession_est', 'seller_cooncession_act',
        'seller_cooncession_diff', 'seller_cooncession_calc', 'title_service_cls_est', 'title_service_cls_act',
        'title_service_cls_diff', 'title_service_cls_calc', 'ins_utl_misc_est', 'ins_utl_misc_act', 'ins_utl_misc_diff',
        'ins_utl_misc_calc', 'percent_commission_est', 'commission_est', 'percent_commission_act', 'commission_act',
        'commission_diff', 'percent_commission_calc', 'commission_calc', 'home_insurance_est', 'home_insurance_act',
        'home_insurance_diff', 'home_insurance_calc'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    
}         