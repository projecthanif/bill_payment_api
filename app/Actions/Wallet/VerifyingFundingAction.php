<?php

namespace App\Actions\Wallet;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Services\PaymentGateWay\Paystack\PaystackPaymentService;
use Exception;
use Illuminate\Support\Facades\DB;

class VerifyingFundingAction
{
    public function __construct(
        public PaystackPaymentService $paymentService,
        public Transaction            $transaction,
        public FundWalletAction       $fundWalletAction,
    )
    {
    }

    public function execute(array $data)
    {
        try {

            return DB::transaction(function () use ($data) {
                if (!array_key_exists('trxref', $data) && !array_key_exists('reference', $data)) {
                    throw new Exception('Trx ref is not valid');
                }

                $transactionDetails = $this->transaction->where('transaction_reference', $data['reference'])->get()->first();

                if ($transactionDetails->transaction_status !== TransactionStatus::Pending->value) {
                    throw new Exception('Trx ref is not pending');
                }

                $validationResponse = $this->paymentService->handleGateWayCallBack($data);

                if (!$validationResponse) {
                    throw new Exception('Payment gateway validation failed');
                }

                return $this->fundWalletAction->execute($validationResponse);
            });

        } catch (Exception $e) {
            return generateApiErrorResponse("Failed due to : " . $e->getMessage());
        }
    }
}
