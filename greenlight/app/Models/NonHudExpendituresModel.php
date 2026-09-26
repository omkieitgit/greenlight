<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;

class NonHudExpendituresModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'non_hud_expenditures';
    #public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_id', 'added_by', 'category', 'sub_category','sub_category_lable','description','amount', 'expenditure_date', 'type','org_name','store_name','created_at'
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

        $store_file_name1 = $this->attributes['store_name'];
        $url = url('document/non_hud_expenditure/' . $store_file_name1);


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
