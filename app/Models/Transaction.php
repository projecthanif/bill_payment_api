<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends AbstractModel
{
    public $fillable = [
        'user_id',
        'bill_id',
        'amount',
        'transaction_type',
        'transaction_status',
        'transaction_reference'
    ];
}
