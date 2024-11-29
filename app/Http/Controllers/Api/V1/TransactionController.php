<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Transaction\TransactionResource;
use App\Models\Transaction;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{

    public ?Authenticatable $user;

    public function __construct(
        public Transaction $transaction
    )
    {
        $this->user = auth()->user();
    }

    public function index(): JsonResponse
    {
        $mutatedTransactionData = TransactionResource::collection(
            $this->user->transactions()
                ->paginate(10)
        );
        return generateApiSuccessResponse('Transactions fetched successfully', $mutatedTransactionData);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== $this->user->id) {
            return generateApiErrorResponse(
                'Transaction Detail not for current user',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
        return generateApiSuccessResponse('Transaction fetched successfully', $transaction);
    }

    public function fundTransactions(): JsonResponse
    {
        $transaction = $this->user->transactions()
            ->where('transaction_type', TransactionType::Fund->value)
            ->paginate(10);

        $mutatedTransactionData = TransactionResource::collection($transaction);

        return generateApiSuccessResponse('Wallet Transactions fetched successfully', $mutatedTransactionData);
    }

    public function purchaseTransactions(): JsonResponse
    {
        $transaction = $this->user->transactions()
            ->where('transaction_type', TransactionType::Purchase->value)
            ->paginate(10);

        $mutatedTransactionData = TransactionResource::collection($transaction);

        return generateApiSuccessResponse('Purchase Transactions fetched successfully', $mutatedTransactionData);
    }
}
