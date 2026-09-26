<?php
/**
 * Created By Rativardhan Singh Sengar  2/20/19 11:44 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 2/12/19 9:30 PM
 */

namespace App\Models;

use App\Helpers\CommonHelper;
use App\Services\UserService;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Http\Request;

class DepositWiredModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'deposit_wired';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'deposit_wired_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    ##protected $guarded = ['added_by'];
    protected $fillable = [
        'house_id','deposit_wired', 'deposit_wired_date', 'paid_by'
        ,'county_deposit','trustee_deposit','user_id', 'created_at'
        , 'added_by'
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

    //    public function setAddedByAttribute($value)
    //    {
    //
    //        echo $value;die;
    //        $this->attributes['added_by'] = 2;
    //    }

}
