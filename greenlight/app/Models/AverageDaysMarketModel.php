<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class AverageDaysMarketModel extends Model
{

    protected $dateFormat = 'U';
    protected $table = 'average_days_market';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'house_id', 'added_by', 'type', 'json'
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
