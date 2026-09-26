<?php
/**
 * Created By Rativardhan Singh Sengar  5/28/19 8:09 PM
 * Copyright (c)  2019.  All rights Reserved
 * Last Modified 5/1/19 1:21 AM
 */

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Database\Eloquent\Model;

class LogsEmailsModel extends Model
{

    protected $connection = 'log_db';
    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'logs_emails';

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id', 'user_id','house_id','mail_type', 'to', 'cc', 'bcc', 'subject', 'body', 'status'
    ];

}
