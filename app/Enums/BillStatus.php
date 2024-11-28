<?php

namespace App\Enums;

enum BillStatus: string
{
    case Successful = 'successful';
    case Failed = 'failed';
}
