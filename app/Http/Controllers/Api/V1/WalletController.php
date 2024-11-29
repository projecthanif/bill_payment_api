<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Wallet\FundWalletAction;
use App\Actions\Wallet\GetCurrentBalanceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Wallet\FundWalletRequest;

class WalletController extends Controller
{

    public function getBalance(GetCurrentBalanceAction $action): \Illuminate\Http\JsonResponse
    {
        $balance = $action->execute();

        $data = [
            'balance' => $balance,
        ];

        return generateApiSuccessResponse("Balance Retrieved Successfully", $data);
    }

    public function fundWallet(FundWalletRequest $request, FundWalletAction $action): \Illuminate\Http\JsonResponse
    {
        return $action->execute($request->validated());
    }
}
