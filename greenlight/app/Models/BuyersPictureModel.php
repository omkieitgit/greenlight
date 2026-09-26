<?php
/**
 * Created By Rativardhan Singh Sengar  6/3/19 1:29 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 6/2/19 12:26 AM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class BuyersPictureModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'buyers_picture';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'buyers_picture_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
         'user_id', 'total_cost', 'number_home', 'number_of_home','buyer_notes', 'created_at', 'updated_at'
    ];



    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }
}
