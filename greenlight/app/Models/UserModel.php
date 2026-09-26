<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class UserModel extends Model
{
    
    public $timestamps = false;

    public function saveUser($data){
        DB::table('users')->insert($data);    
    }

    public function editProfile($data){
    	$id = $data['id'];
        DB::table('users')        		
        		->where(['id'=>$id])
        		->update($data);
    }


}
