<?php
/**
 * Created By Rativardhan Singh Sengar  1/14/19 11:14 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 12/29/18 3:16 PM
 */

namespace App\Models;

use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;

class DocumentRenovationCosts extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;
    protected $dateFormat = 'U';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

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

    protected $table = 'document_renovation_costs';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
   //protected $guarded = ['house_id'];
    protected $fillable = [
        'id', 'house_id', 'added_by', 'categories', 'sub_categories', 'amount', 'document_date', 'org_name', 'store_name', 'description', 'created_at', 'updated_at'

    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];


    public function getUrlAttribute()
    {
        if(empty($this->attributes['store_name']))
            return '';

        $store_file_name = $this->attributes['store_name'];
        $url = url('document/document_renovation_costs/' . $store_file_name);

        return $url;
    }

    public function user()
    {
        return $this->belongsTo(User::class,'added_by','id')->select(["users.id","users.first_name","users.last_name"]);

    }

    public function setUpdatedAtAttribute($date)
    {
        $this->attributes['updated_at'] = strtotime($date);
    }

    public function setCreatedAtAttribute($date)
    {

        $this->attributes['created_at'] = strtotime($date);
    }
}
