<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Mockery\Exception;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{

    public function __construct(
        public Wallet $wallet
    )
    {
    }

    public function getBalance()
    {
        try {
            $user = auth()->user();



        } catch (Exception $e) {
            return generateApiErrorResponse("", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
