<?php
/**
 * Created By Rativardhan Singh Sengar  4/21/19 1:23 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/21/19 1:23 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class PropertyQueueListModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;

    public $timestamps = true;
    protected $dateFormat = 'U';
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $table = 'property_queue_list';
    protected $primaryKey = 'list_id';
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'list_id', 'name', 'user_id'
    ];

}
