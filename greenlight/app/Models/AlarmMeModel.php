<?php
/**
 * Created By Rativardhan Singh Sengar  6/3/19 10:12 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 6/3/19 9:25 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class AlarmMeModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'alarm_me';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'alarm_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
       //  'user_id', 'house_id', 'created_at', 'updated_at'
        'alarm_id', 'user_id', 'house_id', 'is_send_email', 'is_show_alarm', 'notification_date', 'created_at', 'updated_at'
        ,'deleted_at'
    ];


    public function house()
    {
        return $this->belongsTo(PropertyModel::class,"house_id","house_id")->whereNull('deleted_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }

}
