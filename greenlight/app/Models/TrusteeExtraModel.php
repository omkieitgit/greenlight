<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:43 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/4/19 12:43 AM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class TrusteeExtraModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'trustee_extra';

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
       'house_id', 'notes' ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


}
