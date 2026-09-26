<?php
/**
 * Created By Rativardhan Singh Sengar  4/29/19 10:29 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 4/21/19 6:04 PM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class CompSectionHistoryModel extends Model implements AuthenticatableContract, AuthorizableContract
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

    protected $table = 'comp_section_history';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'comp_section_history_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'comp_section_history_id', 'user_id', 'house_id','section_type','section_name','section_value', 'date_by', 'foreign_id'
    ];

    public function houseCompSection()
    {
        return $this->belongsTo(PropertyModel::class,"house_id","house_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }
}
