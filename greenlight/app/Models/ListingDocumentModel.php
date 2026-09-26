<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;

class ListingDocumentModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'listing_document';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_id',  'added_by','document_type','document_date','created_at', 'org_name', 'store_name','description'
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
        $url = url('document/listing_document/' . $store_file_name);

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
