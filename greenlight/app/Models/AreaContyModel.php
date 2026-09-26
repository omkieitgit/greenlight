<?php
/**
 * Created By Rativardhan Singh Sengar  6/3/19 9:15 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 6/3/19 9:15 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class AreaContyModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    // use SoftDeletes;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'area_county';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'area_county_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'area_county_id', 'area_id', 'state', 'area_county', 'created_at', 'updated_at'
    ];


    public function area()
    {
        return $this->belongsTo(AreaModel::class,"area_id","area_id");
    }
}
