<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Storage;
use DB;

class ClientRenovationtModel extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'client_renovation';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_id', 'added_by', 'invoice_date','amount','paid_date','paid'
        ,'section_type','invoice_url','created_at',
        'invoice_lock','bank_deposit_url','bank_statement_url','funder'
    ];


    /**
     * Get the House that owns the Price History.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User',"added_by",'id');
    }

    public function renovation_detail(){
          return $this->hasMany(ClientRenovationDetailModel::class,"invoice_id","id");
    }

    public function funder_info()
    {
        return $this->belongsTo('App\Models\McdLenderModel',"funder",'id');
    }

    public function total_renovation_amount(){
        return $this->hasOne(ClientRenovationDetailModel::class,"invoice_id","id")->select(['invoice_id',DB::raw("SUM(client_renovation_detail.amount) as total_amount")])->groupBy('invoice_id');
    }

    public function invoice_history(){
        return $this->hasMany(InvoiceHistoryModel::class,"invoice_id","id");
    }
}
