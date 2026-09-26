<?php
/**
 * Created By Rativardhan Singh Sengar  5/16/19 11:59 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/16/19 11:59 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class UsersPlansModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;



    protected $table = 'users_plans';

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
        'id', 'user_id', 'plan_region', 'plan_state', 'plan_county', 'plan_price', 'created_at', 'updated_at'
    ];



    public function users_plans()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }
}
