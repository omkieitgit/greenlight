<?php
/**
 * Created By Rativardhan Singh Sengar  4/18/19 7:17 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/15/19 9:48 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class UserFavoritesModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'user_favorites';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'favid';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'house_id', 'user_id'
    ];

    public function setUpdatedAtAttribute($date)
    {
        $this->attributes['updated_at'] = strtotime($date);
    }

    public function setCreatedAtAttribute($date)
    {

        $this->attributes['created_at'] = strtotime($date);
    }

    public function sale_details()
    {
        return $this->hasMany(SaleDetailsModel::class,"house_id","house_id");
    }

}
