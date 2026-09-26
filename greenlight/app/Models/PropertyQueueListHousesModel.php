<?php
/**
 * Created By Rativardhan Singh Sengar  4/21/19 1:26 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/21/19 1:26 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class PropertyQueueListHousesModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;
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

    protected $table = 'property_queue_list_houses';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'property_queue_list_houses_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'list_id', 'user_id', 'house_id'
    ];

    public function house()
    {
        return $this->belongsTo(PropertyModel::class,"house_id","house_id");
    }

}
