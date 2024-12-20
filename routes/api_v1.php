<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Service\AirtimePurchaseController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\User\LoginUserController;
use App\Http\Controllers\Api\V1\User\RegisterUserController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Resources\Api\V1\User\UserResource;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->group(function () {

    Route::get('/', function () {
        $mutatedUserInfo = new UserResource(auth()->user());
        return generateApiSuccessResponse('Current User Info', $mutatedUserInfo);
    })->middleware('auth:sanctum');

    Route::post('/register', RegisterUserController::class);
    Route::post('/login', LoginUserController::class);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('/wallet')->group(function () {
        Route::get('/balance', [WalletController::class, 'getBalance']);
        Route::post('/fund', [WalletController::class, 'fundWallet']);
    });

    Route::prefix('/purchase')->group(function () {
        Route::post('/airtime', AirtimePurchaseController::class);
    });

    Route::prefix('/transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/fund-transaction', [TransactionController::class, 'fundTransactions']);
        Route::get('/purchase-transaction', [TransactionController::class, 'purchaseTransactions']);
    });

});

Route::get('/wallet/paystack/verify', [WalletController::class, 'verifyFunding']);
