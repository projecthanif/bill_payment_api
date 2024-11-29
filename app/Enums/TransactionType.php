<?php

namespace App\Enums;

enum TransactionType: string
{
    case Fund = 'fund';
    case Purchase = 'purchase';
}
