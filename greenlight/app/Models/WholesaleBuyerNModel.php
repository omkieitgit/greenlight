<?php
/**
 * Created By Rativardhan Singh Sengar  2/12/19 8:17 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/12/19 8:15 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class WholesaleBuyerNModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'wholesale_buyer_n';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'wholesale_buyer_n_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
   //protected $guarded = ['house_id'];
    protected $fillable = [
        'house_id','user_id', 'name', 'email'
        ,'receive_update','est_amount','est_percent','est_profit','est_payoff_amount'
        ,'act_amount','act_percent','act_profit','act_payoff_amount', 'added_by', 'created_at'

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

        $this->attributes['created_at'] = time();
    }
}
