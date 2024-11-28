<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends AbstractModel
{
    public $fillable = [
        'user_id',
        'balance',
        'wallet_status'
    ];
}
