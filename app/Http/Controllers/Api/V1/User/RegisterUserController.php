<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Actions\NewRegistrationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\RegisterUserRequest;
use Illuminate\Http\JsonResponse;

class RegisterUserController extends Controller
{
    public function __invoke(RegisterUserRequest $registerUserRequest, NewRegistrationAction $action): JsonResponse
    {
        return $action->execute($registerUserRequest->validated());
    }
}
