<?php
/**
 * Created By Rativardhan Singh Sengar  4/21/19 12:36 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/18/19 8:37 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class GeoModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'geo';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'house_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'house_id', 'address', 'latitude', 'longitude'
    ];

    /**
     * Get the House that owns the Price History.
     */
    public function house()
    {
        return $this->belongsTo(PropertyModel::class,"house_id");
    }
}
