<?php
/**
 * Created By Rativardhan Singh Sengar  5/28/19 8:09 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/1/19 1:21 AM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class PropertyNotesModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;

    protected $dateFormat = 'U';
    public $timestamps = true;
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];
    protected $hidden = [
        'deleted_at'
    ];
    protected $table = 'property_notes';

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
         'house_id', 'user_id', 'notes'
    ];

    public function house()
    {
        return $this->belongsTo(PropertyModel::class,"house_id","house_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }
}
