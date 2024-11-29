<?php

namespace App\Services\PaymentGateWay\Paystack;

use App\Enums\TransactionStatus;
use App\Interface\PaymentInterface;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;
use Symfony\Component\HttpFoundation\Response;

class PaystackPaymentService implements PaymentInterface
{
    public function initializePayment(array $data = []): JsonResponse
    {
        try {
            $data['amount'] = $data['amount'] * 100;
            $data['callback_url'] = env("APP_URL") . "/api/v1/wallet/paystack/verify";

            $response = Http::withHeaders([
                'Cache-Control' => 'no-cache',
                "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY')
            ])->post(env("PAYSTACK_PAYMENT_URL") . "/transaction/initialize", $data);

            $data = $response->json();

            if (!$data['status']) {
                throw new \Exception($data['message']);
            }

            $mutatedData = [
                'access_code' => $data['data']['access_code'],
                'reference' => $data['data']['reference'],
                'authorization_url' => $data['data']['authorization_url'],
            ];

            return generateApiSuccessResponse("Success", $mutatedData, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return generateApiErrorResponse("Error in payment gateway initialization due to" . $e->getMessage());
        }
    }


    public function handleGateWayCallBack(array $data = []): JsonResponse|array|bool
    {
        try {

            $response = Http::withHeaders([
                'Cache-Control' => 'no-cache',
                "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY')
            ])
                ->get(env("PAYSTACK_PAYMENT_URL") . "/transaction/verify/" . $data['reference']);

            $responseData = $response->json();

            if (!$responseData['status']) {
                throw new \Exception($responseData['message']);
            }

            $userData = $responseData['data']['customer'];
            $user = User::where('email', $userData['email'])->get()->first();

            $user->transactions()->where('payment_reference', $data['reference'])->update([
                'transaction_status' => TransactionStatus::Success->value,
                'payment_reference' => $data['trxref']
            ]);

            return [
                'amount' => $responseData['data']['amount'] / 100,
                'user' => $user,
            ];

        } catch (Exception $e) {
            return generateApiErrorResponse("Error in payment gateway record due to" . $e->getMessage());
        }
    }
}
