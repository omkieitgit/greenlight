<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class LoginHistoryModel extends Model
{
    protected $connection = 'log_db';
    protected $dateFormat = 'U';
    protected $table = 'login_history';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'user_id', 'browser', 'ip_address', 'login_time'
        ];
    public $timestamps = false;

    
}
