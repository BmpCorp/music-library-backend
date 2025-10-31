<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!app()->isProduction() && app()->hasDebugModeEnabled()) {
            $debugbarData = app('debugbar')->getData();
            $responseData = json_decode($response->getContent(), true);

            if (isset($debugbarData['queries']['statements']) && is_array($debugbarData['queries']['statements'])) {
                $queries = array_map(fn ($statement) => $statement['sql'], $debugbarData['queries']['statements']);
            } else {
                $queries = [];
            }

            $responseData['debugbar'] = [
                'id' => $debugbarData['__meta']['id'] ?? null,
                'time' => $debugbarData['time']['duration'] ?? null,
                'memory' => $debugbarData['memory']['peak_usage'] ?? null,
                'queries_count' => $debugbarData['queries']['count'] ?? null,
                'queries' => $queries,
                'models_count' => $debugbarData['models']['badges'] ?? null,
            ];

            $response->setContent(json_encode($responseData));
        }

        return $response;
    }
}
