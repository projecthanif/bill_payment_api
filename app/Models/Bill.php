<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends AbstractModel
{
    public $fillable = [
        'user_id',
        'bill_type',
        'amount',
        'bill_status',
        'comment'
    ];
}
