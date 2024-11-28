<?php

namespace App\Enums;

enum WalletStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
    case Suspened = 'suspended';
}
