<?php

namespace App\Actions\Wallet;

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
            $user = $validated['user'];

            DB::transaction(function () use ($validated, $user) {

                $userWallet = $user->wallet()->lockForUpdate()->first();

                $updatedWalletDetails = $userWallet->increment(
                    'balance', $validated['amount'],
                );

                if (!$updatedWalletDetails) {
                    throw new \Exception('Failed to update wallet');
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
