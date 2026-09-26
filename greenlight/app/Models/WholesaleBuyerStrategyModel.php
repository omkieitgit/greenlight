<?php
/**
 * Created By Rativardhan Singh Sengar  2/4/19 12:40 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 10/31/18 8:15 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class WholesaleBuyerStrategyModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'wholesale_buyer_strategy';

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
       'house_id','is_manual_close_date_a_to_b','est_close_date_a_to_b','act_close_date_a_to_b','calc_close_date_a_to_b'
       ,'is_manual_close_date_b_to_c','est_close_date_b_to_c','act_close_date_b_to_c','calc_close_date_b_to_c'
       ,'est_days_start_to_finish','act_days_start_to_finish','calc_days_start_to_finish','est_days_on_market','act_days_on_market'
       ,'calc_days_on_market','est_prp_rate_of_return','act_prp_rate_of_return','calc_prp_rate_of_return'
       ,'est_ann_return_aft_fnl_close','act_ann_return_aft_fnl_close','calc_ann_return_aft_fnl_close'
       ,'est_total_cost_to_buy_a_to_b','act_total_cost_to_buy_a_to_b','calc_total_cost_to_buy_a_to_b'
       ,'est_total_cost_to_buy_b_to_c','act_total_cost_to_buy_b_to_c','calc_total_cost_to_buy_b_to_c','est_net_profit'
       ,'act_net_profit','calc_net_profit','net_payout_per','est_payout_split','act_payout_split','calc_payout_split'
       ,'net_payout_founder','est_net_payout','act_net_payout','calc_net_payout','date_listed','date_under_contract','date_sold'
       ,'actual_days_on_market','lf_dead_property','lf_wo_auction_outbid','upst_auction_no_bid','potential_buy','property_in_escrow'
       ,'list_and_flip','bid_offer_on_property','property_closed','property_closed_date','total_days_to_sell','purchased_deed'
       ,'bidding_in_process','assignment','bid_offer_confirmed','deposit_to_be_returned','exclusive_agency','off_site_or_no_sale'
       ,'property_purchased_acq_a_to_b','dead_property','attended_sale_outbid','attended_sale_no_bid','property_not_purchased','mcd_link'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    public function extra()
    {
        return $this->hasOne(WholesaleBuyerStrategyExtraModel::class,"house_id","house_id");
    }

    public function emails_am()
    {
        return $this->hasMany(EmailsAmModel::class,"house_id","house_id");
    }

    public function emails_company_team_member()
    {
        return $this->hasMany(EmailsCompanyTeamMemberModel::class,"house_id","house_id");
    }
    public function emails_funder_lender()
    {
        return $this->hasMany(EmailsFunderLenderModel::class,"house_id","house_id");
    }
    public function emails_time_left_notice()
    {
        return $this->hasMany(EmailsTimeLeftNoticeModel::class,"house_id","house_id");
    }

    public function sthb()
    {
        return $this->hasMany(WholesaleBuyerNModel::class,"house_id","house_id");
    }

    public function sthb_total()
    {
        return $this->hasOne(WholesaleBuyerNTotalModel::class,"house_id","house_id");
    }

}
