<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class PropertyLenderModel extends Model
{
    
    public $timestamps = false;

    protected $table = 'property_lender';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_id', 'user_id'
    ];

}
