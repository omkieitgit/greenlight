<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:40 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 8:54 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class TrusteeModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'trustee';

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
       'house_id', 'trustee_deposit_returned'
       , 'trustee_deposit_returned_date', 'trustee_returned_to', 'county_deposit_returned'
       , 'county_deposit_returned_date', 'county_returned_to', 'date_cost_wired_due_by'
       , 'amt_wired_close_due_by', 'est_upset_bid_percent', 'est_upset_bid_amount'
       , 'est_upset_bid_manual_ip', 'act_upset_bid_percent', 'act_upset_bid_amount'
       , 'act_upset_bid_manual_ip', 'diff_upset_bid_amount'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];



}
