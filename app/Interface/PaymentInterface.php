<?php

namespace App\Interface;

use Illuminate\Http\JsonResponse;

interface PaymentInterface
{
    public function initializePayment(array $data = []): JsonResponse;

    public function handleGateWayCallBack(array $data = []): JsonResponse|array|bool;
}
