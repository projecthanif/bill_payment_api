<?php

namespace App\Actions;

use App\Enums\WalletStatus;
use App\Http\Resources\Api\V1\User\UserResource;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class NewRegistrationAction
{
    public function __construct(
        public User   $user,
        public Wallet $wallet
    )
    {
    }

    public function execute(array $data): JsonResponse
    {
        try {
            $formatedData = DB::transaction(function () use ($data) {

                $createdUser = $this->user->create($data);

                if (!$createdUser) {
                    throw new JsonException('User failed to create');
                }

                $createdWallet = $this->wallet->create([
                    'user_id' => $createdUser->id,
                    'wallet_status' => WalletStatus::Active->value,
                    'balance' => 0
                ]);

                if (!$createdWallet) {
                    throw new JsonException('Wallet failed to create');
                }

                return new UserResource($createdUser);
            });

            return generateApiSuccessResponse(
                'User and Wallet created successfully',
                $formatedData, statusCode: Response::HTTP_CREATED
            );

        } catch (\Exception $e) {

            return generateApiErrorResponse(
                "An unexpected error occurred: {$e->getMessage()}",
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
