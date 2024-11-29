<?php

namespace App\Http\Requests\Api\V1\Wallet;

use App\Enums\WalletStatus;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;

class FundWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(
        #[CurrentUser] $currentUser,
    ): bool
    {
        return $currentUser->wallet->wallet_status === WalletStatus::Active->value;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            "paymentMethod" => "required",
            "paymentReference" => "required",
        ];
    }
}
