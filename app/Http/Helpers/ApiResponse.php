<?php

namespace App\Http\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse {
    protected function successResponse($message="successful",$data=null,$status=200) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status'  => $status
        ],$status);
    }

    protected function errorResponse($message="bad Request!",$status=400) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'status'  => $status
        ],$status);
    }

    protected function buildPaginatedResourceResponse(string $resource, LengthAwarePaginator $collection): array
    {
        if (!is_subclass_of($resource, JsonResource::class)) {
            throw new \InvalidArgumentException(sprintf('%s must be a subclass of %s.', $resource, JsonResource::class));
        }

        return [
            'data' => $resource::collection($collection),
            'meta' => [
                'total'       => $collection->total(),
                'currentPage' => $collection->currentPage(),
                'lastPage'    => $collection->lastPage(),
                'perPage'     => $collection->perPage(),
            ],
            'links' => [
                'next' => $collection->nextPageUrl(),
                'prev' => $collection->previousPageUrl(),
            ],
        ];
    }
}
