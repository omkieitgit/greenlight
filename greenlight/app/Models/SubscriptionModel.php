<?php
/**
 * Created By Rativardhan Singh Sengar  5/17/19 12:38 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/16/19 11:59 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class SubscriptionModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;



    protected $table = 'subscriptions';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'name', 'braintree_id', 'braintree_plan', 'quantity', 'trial_ends_at', 'ends_at', 'created_at', 'updated_at'
    ];



    public function subscription()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }
}
