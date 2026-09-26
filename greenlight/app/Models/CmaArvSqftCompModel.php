<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmaArvSqftCompModel extends Model
{

    protected $dateFormat = 'U';
    protected $table = 'cma_arv_sqft_comp';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'house_id', 'added_by', 'cma_type', 'sqft_json_data'
        ];
    public $timestamps = true;
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];


    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name"]);
    }

    public function house_id()
    {
        return $this->belongsTo(PropertyModel::class,"house_id","house_id")->select(["users.id","users.first_name","users.last_name"]);
    }
    
}
