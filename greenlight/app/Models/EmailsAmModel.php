<?php
/**
 * Created By Rativardhan Singh Sengar  2/12/19 8:13 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 11/6/18 8:42 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class EmailsAmModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'emails_am';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'emails_am_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
   //protected $guarded = ['house_id'];
    protected $fillable = [
        'house_id','user_id', 'email', 'created_at'
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
