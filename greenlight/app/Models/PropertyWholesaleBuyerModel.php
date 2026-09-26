<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class PropertyWholesaleBuyerModel extends Model
{
    
    public $timestamps = false;
    protected $table = 'property_wholesale_buyer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_id', 'user_id'
    ];


}
