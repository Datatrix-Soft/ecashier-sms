<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsBilling extends Model
{
    // table
    protected $table = 'sms_billings';
    public static function billNumber()
    {
        return time();
    }
}
