<?php
/**
 * Created By Rativardhan Singh Sengar  05/10/2020 5:48 PM
 * Copyright (c)  2018.  All rights Reserved
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffSiteModel extends Model
{
    public $timestamps = true;
    protected $dateFormat = 'U';
    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $table = 'off_site';
    protected $primaryKey = 'house_id';

    # you should use either  $fillable or $guarded - not both
    //    protected $guarded = ['house_id'];


    protected $fillable = [
        'off_site', 'house_id', 'scraper_file_name'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
