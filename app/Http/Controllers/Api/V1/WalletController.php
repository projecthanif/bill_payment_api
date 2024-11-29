<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Wallet\GetCurrentBalanceAction;
use App\Actions\Wallet\InitializeFundWalletAction;
use App\Actions\Wallet\VerifyingFundingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Wallet\FundWalletRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Js;

class WalletController extends Controller
{

    public function getBalance(GetCurrentBalanceAction $action): JsonResponse
    {
        $data['balance'] = $action->execute();

        return generateApiSuccessResponse("Balance Retrieved Successfully", $data);
    }

    public function fundWallet(FundWalletRequest $request, InitializeFundWalletAction $action): JsonResponse
    {
        return $action->execute($request->validated());
    }

    public function verifyFunding(Request $request, VerifyingFundingAction $action): JsonResponse
    {
        $data = $request->all();
        return $action->execute($data);
    }
}
