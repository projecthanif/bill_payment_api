<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

function generateApiSuccessResponse(string $msg = '', mixed $data = null, $statusCode = Response::HTTP_OK): JsonResponse
{
    $jsonData = [
        'message' => $msg,
        'statusCode' => $statusCode
    ];

    if ($data !== null) {
        $jsonData['data'] = $data;
    }


    return response()->json($jsonData, $statusCode);
}


function generateApiErrorResponse(string $msg = '', mixed $data = null, $statusCode = Response::HTTP_NOT_MODIFIED): JsonResponse
{
    $jsonData = [
        'message' => $msg,
        'statusCode' => $statusCode
    ];
    if ($data !== null) {
        $jsonData['data'] = $data;
    }
    return response()->json($jsonData, $statusCode)->header('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate');
}

function generateTestResponse(string $msg = '', mixed $data = null, int $statusCode = Response::HTTP_OK): JsonResponse
{
    return response()->json([
        'message' => $msg,
        'data' => $data,
        'statusCode' => $statusCode
    ]);
}
