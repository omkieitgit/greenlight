<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInviteSettingsModel extends Model
{
    use SoftDeletes;
    protected $dateFormat = 'U';
    protected $table = 'user_invite_settings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'sale_type', 'state', 'county', 'user_id'
        ];
    public $timestamps = true;
    protected $hidden = [
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,"user_id","id")->select(["users.id","users.first_name","users.last_name","users.email"]);
    }
    
    
}
