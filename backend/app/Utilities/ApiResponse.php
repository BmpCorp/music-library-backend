<?php

namespace App\Utilities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public function send($data = null, $statusCode = 200, $description = null): JsonResponse
    {
        $response = [
            'status' => $statusCode === 200 ? 'success' : 'error',
            'status_code' => $statusCode,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($description !== null) {
            $response['description'] = $description;
        }

        return new JsonResponse($response, $statusCode, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Authorization',
        ]);
    }

    public function items($items = [], $data = []): JsonResponse
    {
        return $this->success([
            'items' => $items,
            ...$data,
        ]);
    }

    public function item($item = null, $data = []): JsonResponse
    {
        return $this->success([
            'item' => $item,
            ...$data,
        ]);
    }

    public function pagination(LengthAwarePaginator $paginator, $data = [], $options = []): JsonResponse
    {
        $items = $paginator->items();

        if (!empty($options['append'])) {
            $items = (new Collection($items))->append($options['append'])->all();
        }
        if (!empty($options['hide'])) {
            $items = (new Collection($items))->makeHidden($options['hide'])->all();
        }
        if (!empty($options['map'])) {
            $items = array_map($options['map'], $items);
        }

        return $this->success([
            'items' => $items,
            'pagination' => [
                'total' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'pages' => $paginator->lastPage(),
            ],
            ...$data,
        ]);
    }

    public function success($data = null, $description = ''): JsonResponse
    {
        return $this->send($data, 200, $description);
    }

    public function notFound($description = 'Not found'): JsonResponse
    {
        return $this->send(null, 404, $description);
    }

    public function unauthorized($description = 'Unauthorized'): JsonResponse
    {
        return $this->send(null, 401, $description);
    }

    public function badRequest($description = 'Bad request'): JsonResponse
    {
        return $this->send(null, 400, $description);
    }

    public function forbidden($description = 'Forbidden'): JsonResponse
    {
        return $this->send(null, 403, $description);
    }

    public function internalServerError($description = 'Internal Server Error'): JsonResponse
    {
        return $this->send(null, 500, $description);
    }

    public function tooManyRequests($description = 'Too many requests'): JsonResponse
    {
        return $this->send(null, 429, $description);
    }
}
