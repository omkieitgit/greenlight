<?php
/**
 * Created By Rativardhan Singh Sengar  4/15/19 9:48 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/14/19 11:48 PM
 */

namespace App\Models;

use App\Helpers\CommonHelper;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class IncidentalCostsModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'incidental_costs';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id', 'house_id','categories', 'title', 'cost', 'monthly', 'weekly', 'daily', 'created_at', 'updated_at'
    ];

    public function setUpdatedAtAttribute($date)
    {
        $this->attributes['updated_at'] = strtotime($date);
    }

    public function setCreatedAtAttribute($date)
    {

        $this->attributes['created_at'] = strtotime($date);
    }

}
