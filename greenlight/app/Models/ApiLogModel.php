<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class ApiLogModel extends Model
{
    protected $connection = 'log_db';
    protected $dateFormat = 'U';
    protected $table = 'api_logs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'uri', 'method', 'params', 'api_key', 'ip_address', 'time', 'rtime', 'authorized', 'response_code'
        ];
    public $timestamps = true;

    
}
