<?php
/**
 * Created By Rativardhan Singh Sengar  2/22/19 2:43 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/5/19 5:10 PM
 */

namespace App\Models;

use App\Models\RolesModel;
use App\Models\UserRolesModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
   // use Billable;

    #public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',  'email', 'username', 'password', 'current_role', 'status', 'is_payed'
        , 'is_agree', 'is_popup', 'first_name', 'last_name', 'address', 'city', 'state'
        , 'mobile', 'last_login', 'work_profile_team','created_at', 'updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password','master_password'
    ];

    public function setUpdatedAtAttribute($date)
    {
        $this->attributes['updated_at'] = strtotime($date);
    }

    public function setCreatedAtAttribute($date)
    {
        $this->attributes['created_at'] = strtotime($date);
    }

    public function user_roles()
    {
        return $this->hasManyThrough(RolesModel::class, UserRolesModel::class
            ,'user_id','id','id','role_id');
    }

    public function roles()
    {
        return $this->belongsToMany(RolesModel::class, 'user_roles', 'user_id', 'role_id');
    }

    //---Added by Varsha for auth.admin middleware

    // Define isAdmin method to check if the user is an admin
    public function isAdmin()
    {
        return $this->current_role === 'admin'; // Assuming you have a 'current_role' column in your users table
    }


    //    /**
    //     * The "booting" method of the model.
    //     *
    //     * @return void
    //     */
    //    protected static function boot()
    //    {
    //        parent::boot();
    //        static::addGlobalScope('activeStatus', function (Builder $builder) {
    //            $builder->where('status', '=', 'active');
    //        });
    //    }

}
