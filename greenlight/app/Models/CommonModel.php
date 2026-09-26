<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class CommonModel extends Model
{
    
    public $timestamps = false;
    protected $table = '';

    public function __construct($temp_table) {
        $this->table = $temp_table;
    }

    /**
     *
     * Common function to save info into database
     *
     * @param $data
     * @param array $where
     * @return bool|int
     */
    public function saveInfo($data, $where = array()){

        if(empty($this->table))
            return FALSE;

        if(empty($where))
        {
            $user_id = DB::table($this->table)->insertGetId($data);
            return $user_id;
        }
        else
        {
            if(!empty($where))
            {
                DB::table($this->table)
                    ->where($where)
                    ->update($data);
                return TRUE;
            }

        }
        return FALSE;
    }

    /**
     * Function to return info of db table for one row only with where condition only
     * @param array $where
     * @return bool
     */
    public function getInfo($where = array())
    {
        if(empty($this->table))
            return FALSE;

        if(empty($where))
        {
            return FALSE;
        }
        else
        {
            return DB::table($this->table)
                ->where($where)->get()->first();
        }

    }

    /**
     * Function to return all info of db table
     * @param array $where
     * @return bool
     */
    public function getAllInfo($where = array(), $pagination = array())
    {
        if(empty($this->table))
            return FALSE;

        if(empty($where))
        {
            return FALSE;
        }
        else
        {
            return DB::table($this->table)
                ->where($where)->get();
        }

    }

    /**
     * Function to return all info of db table
     * @param array $where
     * @return bool
     */
    public function deleteInfo($where = array())
    {
        if(empty($this->table))
            return FALSE;

        if(empty($where))
        {
            return FALSE;
        }
        else
        {
            return DB::table($this->table)
                ->where($where)->delete();
        }

    }


}
