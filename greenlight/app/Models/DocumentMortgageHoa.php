<?php
/**
 * Created By Rativardhan Singh Sengar  1/18/19 1:10 AM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 1/17/19 1:03 AM
 */

namespace App\Models;

use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;

class DocumentMortgageHoa extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;

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

    protected $table = 'document_mortgage_hoa';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'document_mortgage_hoa_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
   //protected $guarded = ['house_id'];
    protected $fillable = [
       'house_id', 'added_by', 'org_name', 'store_name', 'case_number','document_date', 'document_type', 'other_name', 'book_page', 'lien_amount', 'created_at'
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
        $url = url('document/document_mortgage_hoa/' . $store_file_name);
        return $url;

    }

    public function user()
    {
        return $this->belongsTo(User::class,'added_by','id')->select(["users.id","users.first_name","users.last_name"]);
    }
}
