<?php

namespace App\Actions\Wallet;

use Illuminate\Auth\AuthenticationException;
use Mockery\Exception;
use Symfony\Component\HttpFoundation\Response;

class GetCurrentBalanceAction
{

    public function execute(): mixed
    {
        try {
            $user = auth()->user();

            if (!$user) {
                throw new AuthenticationException("User not found");
            }

            $balance = $user->wallet->balance ?? null;

            if ($balance === null) {
                throw new Exception('Balance not Available');
            }

            return $balance;

        } catch (AuthenticationException $authException) {
            return generateApiErrorResponse(
                "Authentication Error: {$authException->getMessage()}",
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );

        } catch (Exception $e) {
            return generateApiErrorResponse(
                "Could not retrieved balance data due to " . $e->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
