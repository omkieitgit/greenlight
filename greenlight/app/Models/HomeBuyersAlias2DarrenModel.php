<?php
/**
 * Created By Rativardhan Singh Sengar  3/6/19 7:50 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 3/6/19 7:49 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class HomeBuyersAlias2DarrenModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'home_buyers_alias2_darren';

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
        'house_id', 'office_fee_check', 'office_fee_date', 'lmod_check', 'lmod_date', 'auction_check', 'auction_date', 'llc_check', 'llc_date', 'accounting_check', 'accounting_date', 'travel_check', 'travel_date', 'web_fee_check', 'web_fee_check_date', 'data_input_check', 'data_input_date', 'inspection_check', 'inspection_date'
        ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


}         


