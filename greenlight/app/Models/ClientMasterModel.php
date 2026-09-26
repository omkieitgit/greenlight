<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class ClientMasterModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'client_master';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'AA_name','house_id', 'referrer_name','lender_wire_route_account','lender_llc_interest','lender_gross_interest', 'after_30_days', 'bank_charges','open_field_1','dump_fee','insurance','utilities_internet_cameras_arlo_pro','legal_fees','llc_fees','interest_expense','renovation','landscaping','miscellaneous','total_cost_a_b','total_cost_b_c','net_profit','wired_to_trust','member1','member2','member3', 'created_at', 'updated_at'
    ];



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
