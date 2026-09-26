<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:46 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/20/19 11:45 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class AdditionalCostWiredModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'additional_cost_wired';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'additional_cost_wired_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

   //protected $guarded = ['house_id'];
    protected $fillable = [
        'house_id','deposit_wired', 'deposit_wired_date', 'paid_by'
        ,'user_id','added_by', 'created_at'

    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['added_by'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['created_at'] = time();
    }
}
