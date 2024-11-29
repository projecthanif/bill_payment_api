<?php

namespace App\Actions\Wallet;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Services\PaymentGateWay\Paystack\PaystackPaymentService;
use Illuminate\Support\Str;
use Mockery\Exception;

class InitializeFundWalletAction
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public PaystackPaymentService $paymentService,
        public Transaction            $transaction,
    )
    {
    }

    public function execute(mixed $validatedData)
    {
        try {

            $user = auth()->user();

            $ref = Str::uuid()->toString();

            $data = [
                'email' => $user->email,
                'amount' => $validatedData['amount'],
                'reference' => $ref,
            ];

            $user->transactions()->create([
                'amount' => $validatedData['amount'],
                'payment_method' => 'paystack',
                'transaction_reference' => $ref,
                'transaction_type' => TransactionType::Fund->value,
                'transaction_status' => TransactionStatus::Pending->value,
            ]);

            return $this->paymentService->initializePayment($data);

        } catch (Exception $e) {

        }
    }
}
