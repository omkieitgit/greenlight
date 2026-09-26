<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use DB;
class PayoutModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'payout';
    #public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_id','close_date_a_to_b','close_date_b_to_c','hud_a_to_b',
        'purchase_price','created_at','aa_fee','aa_fee_paid','buyer_ref_fee',
        'buyer_ref_fee_paid','bonus_on_spread','travel_office_fee','labor_charges',
        'office_fee','bookkeeping_fee','web_fee','assignment_fee','selling_price_btoc','hud_btoc',
        'interest_expenses','llc_name','financing_cost','distribution_llc_name'
        ,'est_close_date_a_to_b'
        ,'buyer_close_date_a_to_b'
        ,'est_close_date_b_to_c'
        ,'buyer_close_date_b_to_c'
        ,'est_aa_fee'
        ,'buyer_aa_fee'
        ,'homebuyer_ref_fee'
        ,'est_buyer_ref_fee'
        ,'est_travel_office_fee'
        ,'buyer_travel_office_fee'
        ,'est_office_fee'
        ,'buyer_office_fee'
        ,'est_labor_charges'
        ,'buyer_labor_charges'
        ,'est_bookkeeping_fee'
        ,'buyer_bookkeeping_fee'
        ,'est_web_fee'
        ,'buyer_web_fee'
        ,'est_assignment_fee'
        ,'buyer_assignment_fee'
        ,'est_selling_price_btoc'
        ,'buyer_selling_price_btoc'
        ,'est_purchase_price'
        ,'buyer_purchase_price'
        ,'est_bonus_on_spread'
        ,'buyer_bonus_on_spread'
        ,'buyer_funds_prior_closing'
        ,'est_funds_prior_closing'
    ];



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        //$this->attributes['created_at'] = time();
    }

    public function payoutDetail(){
        return $this->hasMany(PayoutDetailModel::class,"house_id","house_id"); //->orderBy('taxes_year','desc');
    }

    public function additionField(){
        return $this->hasMany(AdditionalFieldModel::class,"house_id","house_id"); //->orderBy('taxes_year','desc');
    }

    public function distribution_llc(){
        return $this->hasOne(PayoutLenderModel::class,"house_id","house_id"); //->orderBy('taxes_year','desc');
    }

    public function total_air_bnb(){
        return $this->hasMany(ShortTermRentalModel::class,"house_id","house_id")
        ->select(['house_id',DB::raw('sum(amount_received) as totalAmountReceived')])
        ->where('rental_type','rental')->groupBy('house_id');

    }

    public function other_mcd_detail(){
        return $this->hasOne(mcdOtherInfoModel::class,"house_id","house_id"); //->orderBy('taxes_year','desc');
    }

    function property_info(){
        return $this->hasOne(PropertyModel::class,"house_id","house_id"); 
    }

    public function property_lender(){
        return $this->hasMany(McdLenderModel::class,"house_id","house_id")->where('is_lender','1')->orWhere('is_craig_per','1');
    }
}
