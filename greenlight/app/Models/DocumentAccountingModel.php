<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;

class DocumentAccountingModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use SoftDeletes;

    protected $table = 'document_accounting';
    #ToDO; some places has old date format, change into epoc time for date field
    # this settings use for epoc time below two lines
    protected $dateFormat = 'U';
    public $timestamps = false;
    protected $fillable = [
        'house_id',  'added_by','document_type','amount','document_date','created_at', 'org_name', 'store_name','description'
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
        $url = url('document/document_accounting/' . $store_file_name);

        return $url;

    }


    /**
     * Get the House that owns the Price History.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User',"added_by",'id');
    }
}
