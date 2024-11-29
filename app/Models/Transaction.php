<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends AbstractModel
{
    public $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'payment_reference',
        'transaction_type',
        'transaction_status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
