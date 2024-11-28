<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\LoginRequest;
use App\Http\Resources\Api\V1\User\UserResource;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class LoginUserController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        try {
            $isAuth = auth()->attempt($request->validated());

            if (!auth()->check() || !$isAuth) {
                throw new AuthenticationException('Authentication failed');
            }

            $user = auth()->user();
            $token = $user->createToken('user token') ?? null;

            if (!$token) {
                throw new \Exception('Access Token failed to create');
            }
            $accessCredentials = [
                'token' => $token->plainTextToken,
                'expires_in' => '60 minutes',
                'type' => 'Bearer',
            ];

            $mutatedUserInfo = new UserResource($user);

            $data = [
                'userInfo' => $mutatedUserInfo,
                'accessCredentials' => $accessCredentials,
            ];

            return generateApiSuccessResponse('User Logged In Successfully', $data);

        } catch (AuthenticationException $authenticationException) {

            return generateApiErrorResponse(
                "Authentication Failed due to: {$authenticationException->getMessage()}",
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );

        } catch (\Exception $exception) {

            return generateApiErrorResponse(
                "Login Failed due to: {$exception->getMessage()}",
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );

        }
    }
}
