<?php
/**
 * Created By Rativardhan Singh Sengar  10/31/18 7:55 PM
 * Copyright (c)  2018.  All rights Reserved
 * Last Modified 10/29/18 9:28 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class LocalRealEstateModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'local_real_estate';

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
        'house_id', 'zillow_url', 'zestimate'
        , 'redfin_url', 'redfin_est'
        , 'realtor_url', 'realtor_est'
        , 'truila_url', 'truila_est'
        , 'har_url', 'har_est' 
        , 'gui_url', 'gui_est','movoto_url','movoto_est'
        , 'beenverified_url'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


}
