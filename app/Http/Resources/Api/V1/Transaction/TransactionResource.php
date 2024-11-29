<?php

namespace App\Http\Resources\Api\V1\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'paymentMethod' => $this->payment_method,
            'paymentReference' => $this->payment_reference,
            'transactionType' => $this->transaction_type,
            'status' => $this->transaction_status,
        ];
    }
}
