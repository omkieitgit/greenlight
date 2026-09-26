<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;
use DB;

class InvoiceHistoryModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
      
    protected $table = 'invoice_history';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'house_id', 'invoice_id', 'user_id', 'created_at','updated_at'
        ];
        
    //public $timestamps = false;

     /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';


    /**
     * Get the House that owns the Price History.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User',"user_id",'id')->select(['id','first_name','last_name']);
    }
}
