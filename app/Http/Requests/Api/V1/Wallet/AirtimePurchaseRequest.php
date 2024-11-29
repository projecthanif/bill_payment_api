<?php

namespace App\Http\Requests\Api\V1\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class AirtimePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'network_type' => 'required|string',
            'phone_number' => 'required|string',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'network_type' => $this->networkType,
            'phone_number' => $this->phoneNumber
        ]);
    }
}
