<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ResponseTrait
{
    public function apiResponse(int $status_code, bool $success, mixed $data = [], string $message = '', mixed $meta = null): JsonResponse
    {
        $response = [];
        $response['success'] = $success;
        $response['data'] = $data['data'] ?? $data;
        if (! empty($meta)) {
            if ($meta instanceof LengthAwarePaginator) {
                $response['meta'] = [
                    'current_page' => $meta->currentPage() ?? null,
                    'from' => $meta->firstItem() ?? null,
                    'to' => $meta->lastItem() ?? null,
                    'total' => $meta->total() ?? null,
                    'per_page' => $meta->perPage() ?? null,
                    'last_page' => $meta->lastPage() ?? null,
                    'next_page_url' => $meta->nextPageUrl() ?? null,
                    'first_page_url' => ! empty($meta->firstItem()) ? $meta->url($meta->firstItem()) ?? null : null,
                    'last_page_url' => ! empty($meta->lastPage()) ? $meta->url($meta->lastPage()) ?? null : null,
                ];
            } else {
                $response['meta'] = $meta;
            }
        }

        if (! empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response)->setStatusCode($status_code);
    }
}
