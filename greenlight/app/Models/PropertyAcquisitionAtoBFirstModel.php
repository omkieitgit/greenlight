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


class PropertyAcquisitionAtoBFirstModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'property_acquisition_a_to_b_first';

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
        'house_id', 'is_manual_contract_purchase_price_est', 'contract_purchase_price_est', 'is_manual_contract_purchase_price_act', 'contract_purchase_price_act', 'contract_purchase_price_diff', 'contract_purchase_price_calc', 'attorney_fees_litigation_est', 'attorney_fees_litigation_act', 'attorney_fees_litigation_diff', 'attorney_fees_litigation_calc', 'hud_fees_buyer_est', 'hud_fees_buyer_act', 'hud_fees_buyer_diff', 'hud_fees_buyer_calc', 'lenders_title_insurance_est', 'lenders_title_insurance_act', 'lenders_title_insurance_diff', 'lenders_title_insurance_calc', 'owner_title_insurance_est', 'owner_title_insurance_act', 'owner_title_insurance_diff', 'owner_title_insurance_calc', 'recording_est', 'recording_act', 'recording_diff', 'recording_calc', 'property_taxes_est', 'property_taxes_act', 'property_taxes_diff', 'property_taxes_calc', 'office_fee_est', 'office_fee_act', 'office_fee_diff', 'office_fee_calc', 'office_fee_calc', 'is_manual_loss_mitigation_on_deposits_est', 'loss_mitigation_on_deposits_est', 'loss_mitigation_on_deposits_act', 'loss_mitigation_on_deposits_diff', 'loss_mitigation_on_deposits_calc', 'sale_fee_est', 'sale_fee_act', 'sale_fee_diff', 'sale_fee_calc', 'llc_changes_est', 'llc_changes_act', 'llc_changes_diff', 'llc_changes_calc', 'utilities_est', 'utilities_act', 'utilities_diff', 'utilities_calc', 'is_manual_insurance_est', 'insurance_est', 'insurance_act', 'insurance_diff', 'insurance_calc', 'wire_fees_est', 'wire_fees_act', 'wire_fees_diff', 'wire_fees_calc', 'airport_transport_wire_est', 'airport_transport_wire_act', 'airport_transport_wire_diff', 'airport_transport_wire_calc', 'is_manual_excise_tax_nc_wire_est', 'excise_tax_nc_wire_est', 'excise_tax_nc_wire_act', 'excise_tax_nc_wire_diff', 'excise_tax_nc_wire_calc'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    
}         