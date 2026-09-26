<?php
/**
 * Created By Rativardhan Singh Sengar  5/27/19 1:44 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/3/19 11:44 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class EmailSettingsModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'email_settings';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'email_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
   //protected $guarded = ['house_id'];
     protected $fillable = [
        'ename', 'email_type', 'email', 'county', 'added_by', 'created_at', 'updated_at'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


}
