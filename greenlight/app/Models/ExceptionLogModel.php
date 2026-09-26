<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ExceptionLogModel extends Model
{

    protected $connection = 'log_db';
    protected $dateFormat = 'U';
    protected $table = 'exception_logs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'uri', 'method', 'params',  'ip_address', 'authorized',
        'response_code',
        'error'
        ];
    public $timestamps = true;

    
}
