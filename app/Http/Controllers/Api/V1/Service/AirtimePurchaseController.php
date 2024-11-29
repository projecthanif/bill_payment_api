<?php

namespace App\Http\Controllers\Api\V1\Service;

use App\Actions\Wallet\AirtimePurchaseAction;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Wallet\AirtimePurchaseRequest;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AirtimePurchaseController extends Controller
{
    public function __construct(
        public Wallet $wallet,
    )
    {
    }

    public function __invoke(AirtimePurchaseRequest $request, AirtimePurchaseAction $action)
    {

        $validatedData = $request->validated();

        $user = auth()->user();

        $compare = $validatedData['amount'] <= $user->wallet->balance;

        if (!$compare) {
            $comment = 'Wallet balance low';

            $ref = Str::uuid()->toString();

            $user->transactions()->create([
                'amount' => $validatedData['amount'],
                'payment_method' => 'wallet',
                'transaction_reference' => $ref,
                'transaction_type' => TransactionType::Purchase->value,
                'transaction_status' => TransactionStatus::Failed->value,
                'comment' => $comment,
            ]);

            $data = ['balance' => $user->wallet->balance];

            return generateApiErrorResponse(
                $comment,
                $data,
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        return $action->execute($validatedData);
    }
}
