<?php

namespace App\Actions\Wallet;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Resources\Api\V1\Wallet\WalletResource;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FundWalletAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public Wallet $wallet,
    )
    {
    }

    public function execute(array $validated): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth()->user();

            DB::transaction(function () use ($validated, $user) {

                $userWallet = $user->wallet()->lockForUpdate()->first();

                $updatedWalletDetails = $userWallet->increment(
                    'balance', $validated['amount'],
                );

                if (!$updatedWalletDetails) {
                    throw new \Exception('Failed to update wallet');
                }

                $createdTransactionDetails = $user->transactions()->create([
                    'amount' => $validated['amount'],
                    'payment_method' => $validated['paymentMethod'],
                    'payment_reference' => $validated['paymentReference'],
                    'transaction_type' => TransactionType::Fund->value,
                    'transaction_status' => TransactionStatus::Success->value,
                ]);

                if (!$createdTransactionDetails) {
                    throw new \Exception('Failed to make transaction');
                }

                return $updatedWalletDetails;
            });

            $mutatedWalletDetails = new WalletResource($user->wallet);

            return generateApiSuccessResponse(
                msg: 'Wallet Funded Successfully',
                data: [$mutatedWalletDetails],
                statusCode: Response::HTTP_CREATED
            );

        } catch (\Exception $e) {
            return generateApiErrorResponse(
                msg: "Error due to: {$e->getMessage()}",
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }


    }
}
