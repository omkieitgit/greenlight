<?php
/**
 * Created By Mranalinee Chouhan  18/04/19 11:40 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 8:54 PM
 */


namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class ClientRenovationBudgetModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    #public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'client_renovation_budget';

    

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    //protected $guarded = ['house_id'];
    protected $fillable = [
       'house_id', 'client_budget_renovation'
       , 'budget_renovation', 'est_budge_renovation', 'renovation_start'
       , 'client_day_renovation', 'lender_fund_renovation', 'actual_days_finish'
       , 'actual_budget_renovation', 'renovation_finish', 'est_day_finish'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];



}
