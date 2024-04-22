<?php

namespace App\Supports\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait ResponseStatus
 *
 * @package App\Supports\Traits
 */
trait ResponseStatus
{
    /**
     * response status success
     *
     * @param array $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function responseStatusSuccess($data = [], string $message = 'successfully', int $statusCode = 200)
    {
        return $this->responseExpected($statusCode, $message, $data, true);
    }

    /**
     * response status failed
     *
     * @param int $statusCode
     * @param string $message
     * @param array $data
     * @return JsonResponse
     */
    protected function responseStatusFailed(int $statusCode, string $message, $data = [])
    {
        return $this->responseExpected($statusCode, $message, $data, false);
    }

    /**
     * response status expected
     *
     * @param int $statusCode
     * @param string $message
     * @param array $data
     * @param bool $status
     * @return JsonResponse
     */
    private function responseExpected(int $statusCode, string $message, $data, bool $status)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
