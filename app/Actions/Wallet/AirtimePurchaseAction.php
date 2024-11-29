<?php

namespace App\Actions\Wallet;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AirtimePurchaseAction
{
    public array $transactionData = [];

    public function __construct(
        public Wallet      $wallet,
        public Transaction $transaction,
    )
    {
    }

    public function execute(mixed $validatedData): JsonResponse
    {
        try {
            $transactionHistory = DB::transaction(function () use ($validatedData) {

                $transactionHistory = $this->makeTransactionHistory(['amount' => $validatedData['amount']]);

                $validatedData['reference'] = $transactionHistory->transaction_reference;
                $airtimePurchaseData = $this->makeAirtimePurchase($validatedData);

//                $airtimePurchaseData['status'] = false;
//                $airtimePurchaseData['message'] = "Failed due to service not available";

                $this->transactionData['payment_reference'] = $airtimePurchaseData['payment_reference'];

                if (!$airtimePurchaseData['status']) {

                    $transactionHistory->update([
                        'transaction_status' => TransactionStatus::Failed->value,
                        'comment' => $airtimePurchaseData['message'],
                    ]);

                    throw new \Exception($airtimePurchaseData['message']);
                }

                $this->updateWalletBalance($airtimePurchaseData['amount']);
                $transactionHistory->update([
                    'transaction_status' => TransactionStatus::Success->value,
                ]);

                return $transactionHistory;
            });

            return generateApiSuccessResponse('Airtime Purchase Successful', [
                'invoice' => $transactionHistory,
            ]);

        } catch (\Exception $exception) {
            return generateApiErrorResponse(
                'Failed ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function makeAirtimePurchase(array $data): array
    {
        $response = [];

        ////
        /// Business logic for purchasing airtime goes here from an endpoint
        ///

        $response['status'] = true;
        $response['amount'] = $data['amount'];
        $response['payment_reference'] = Str::random();

        return $response;
    }

    public function makeTransactionHistory(array $data): Transaction
    {
        $user = auth()->user();
        $ref = Str::uuid()->toString();

        return $user->transactions()->create([
            'amount' => $data['amount'],
            'payment_method' => 'wallet',
            'transaction_reference' => $ref,
            'transaction_type' => TransactionType::Purchase->value,
            'transaction_status' => TransactionStatus::Pending->value,
        ]);
    }

    public function updateWalletBalance(float $amount): bool
    {
        $user = auth()->user();
        return $user->wallet->decrement('balance', $amount);
    }
}
