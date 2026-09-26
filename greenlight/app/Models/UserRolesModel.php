<?php
/**
 * Created By Rativardhan Singh Sengar  1/5/19 4:37 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/14/18 11:14 AM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class UserRolesModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'user_roles';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    # you should use either  $fillable or $guarded - not both
    //    protected $guarded = ['house_id'];


    protected $fillable = [
         'user_id', 'role_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    public function role()
    {
        return $this->hasOne(RolesModel::class,"id","role_id");
    }

}



